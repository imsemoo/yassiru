<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCircleRequest;
use App\Models\CircleMember;
use App\Models\DigitalContract;
use App\Models\FundCircle;
use App\Models\Guarantor;
use App\Services\FundCircleService;
use App\Services\GuaranteeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FundController extends Controller
{
    public function __construct(
        private FundCircleService $fundService,
        private GuaranteeService $guaranteeService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $query = FundCircle::with('city:id,name')
            ->withCount('members')
            ->whereIn('status', ['forming', 'active']);

        if ($request->city_id) {
            $query->where('city_id', $request->city_id);
        }

        $circles = $query->latest()->paginate(12);

        // Tag each circle with whether the current user is already a member
        $memberCircleIds = CircleMember::where('user_id', $userId)
            ->pluck('circle_id')
            ->flip();

        $circles->getCollection()->transform(function ($circle) use ($memberCircleIds) {
            $circle->is_member = $memberCircleIds->has($circle->id);
            $circle->is_full = $circle->members_count >= $circle->max_members;
            return $circle;
        });

        return response()->json($circles);
    }

    public function store(StoreCircleRequest $request): JsonResponse
    {
        $user = $request->user();

        // Admins can create circles without a certificate (for oversight/seeding).
        // Regular users must have completed the qualification course.
        if ($user->role !== 'admin' && !$user->has_certificate) {
            return response()->json(['message' => 'يجب الحصول على شهادة التأهيل أولاً'], 403);
        }

        $circle = $this->fundService->createCircle($request->validated(), $user->id);

        return response()->json(['message' => 'تم إنشاء الحلقة بنجاح', 'circle' => $circle->load('city')], 201);
    }

    public function show(FundCircle $circle): JsonResponse
    {
        $circle->load(['city:id,name', 'members.user:id,name']);

        return response()->json($circle);
    }

    public function join(Request $request, FundCircle $circle): JsonResponse
    {
        $result = $this->fundService->joinCircle($circle, $request->user()->id);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 422);
        }

        return response()->json($result);
    }

    public function dashboard(Request $request, FundCircle $circle): JsonResponse
    {
        Gate::authorize('view', $circle);

        $data = $this->fundService->getDashboardData($circle, $request->user()->id);

        return response()->json($data);
    }

    public function contribute(Request $request, FundCircle $circle): JsonResponse
    {
        $request->validate([
            'payment_ref' => 'nullable|string|max:255',
        ]);

        $result = $this->fundService->recordContribution($circle, $request->user()->id, $request->payment_ref);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 422);
        }

        return response()->json(['message' => $result['message']]);
    }

    // === Contract endpoints ===

    public function getContract(Request $request, FundCircle $circle): JsonResponse
    {
        $contract = DigitalContract::where('circle_id', $circle->id)
            ->where('user_id', $request->user()->id)
            ->first() ?? abort(403, 'لست عضواً في هذه الحلقة أو لا يوجد عقد');

        return response()->json($contract);
    }

    public function signContract(Request $request, FundCircle $circle): JsonResponse
    {
        $contract = DigitalContract::where('circle_id', $circle->id)
            ->where('user_id', $request->user()->id)
            ->first() ?? abort(403, 'لست عضواً في هذه الحلقة أو لا يوجد عقد');

        if ($contract->accepted) {
            return response()->json(['message' => 'تم توقيع العقد مسبقاً'], 422);
        }

        $this->guaranteeService->signContract(
            $contract,
            $request->ip(),
            $request->userAgent(),
            $request->input('device_fingerprint')
        );

        // Check if circle is ready to start
        $this->fundService->checkReadyToStart($circle);

        return response()->json(['message' => 'تم توقيع العقد بنجاح']);
    }

    // === Guarantor endpoints ===

    public function addGuarantor(Request $request, FundCircle $circle): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'national_id' => 'required|string|max:20',
            'relation' => 'required|string|max:100',
        ]);

        $member = CircleMember::where('circle_id', $circle->id)
            ->where('user_id', $request->user()->id)
            ->first() ?? abort(403, 'لست عضواً في هذه الحلقة أو لا يوجد عقد');

        if ($member->has_guarantor) {
            return response()->json(['message' => 'لديك ضامن مسجل بالفعل'], 422);
        }

        $guarantor = $this->guaranteeService->registerGuarantor($member, $request->only([
            'name', 'phone', 'national_id', 'relation',
        ]));

        return response()->json([
            'message' => 'تم تسجيل الضامن. سيتم إرسال رمز تأكيد على هاتفه',
            'guarantor_id' => $guarantor->id,
        ], 201);
    }

    public function confirmGuarantor(Request $request, Guarantor $guarantor): JsonResponse
    {
        // Verify the guarantor belongs to the authenticated user's membership
        if ($guarantor->member->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح بهذا الإجراء'], 403);
        }

        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $result = $this->guaranteeService->confirmGuarantor($guarantor, $request->otp, $request->ip());

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 422);
        }

        // Check if circle is ready to start
        $circle = $guarantor->member->circle_id
            ? FundCircle::find($guarantor->member->circle_id)
            : null;

        if ($circle) {
            $this->fundService->checkReadyToStart($circle);
        }

        return response()->json(['message' => $result['message']]);
    }

    // === Guarantee fund deposit ===

    public function payGuaranteeFee(Request $request, FundCircle $circle): JsonResponse
    {
        $member = CircleMember::where('circle_id', $circle->id)
            ->where('user_id', $request->user()->id)
            ->first() ?? abort(403, 'لست عضواً في هذه الحلقة أو لا يوجد عقد');

        if ($member->guarantee_deposit > 0) {
            return response()->json(['message' => 'تم دفع رسوم الضمان مسبقاً'], 422);
        }

        $transaction = $this->guaranteeService->depositGuaranteeFee($circle, $member);

        return response()->json([
            'message' => 'تم دفع رسوم الضمان بنجاح',
            'amount' => $transaction->amount,
        ]);
    }
}
