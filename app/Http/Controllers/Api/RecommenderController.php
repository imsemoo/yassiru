<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCandidateRequest;
use App\Http\Requests\StoreRecommendationRequest;
use App\Http\Requests\StoreRecommenderRequest;
use App\Http\Resources\UserResource;
use App\Models\Candidate;
use App\Models\CircleMember;
use App\Models\FamilyRequest;
use App\Models\Recommendation;
use App\Models\Recommender;
use App\Models\WeddingRegistration;
use App\Services\CompatibilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecommenderController extends Controller
{
    public function __construct(
        private CompatibilityService $compatibilityService,
    ) {}

    public function register(StoreRecommenderRequest $request): JsonResponse
    {
        $user = $request->user();

        if (Recommender::where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'أنت مسجل كمعرّف بالفعل'], 422);
        }

        // Block role change if user has active obligations as a regular user
        $activeCircles = CircleMember::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->whereHas('circle', fn ($q) => $q->whereIn('status', ['forming', 'active']))
            ->count();

        $pendingWeddings = WeddingRegistration::where('user_id', $user->id)
            ->whereIn('payment_status', ['pending', 'partial'])
            ->count();

        if ($activeCircles > 0 || $pendingWeddings > 0) {
            return response()->json([
                'message' => 'لا يمكنك التسجيل كمعرّف لوجود التزامات نشطة. يرجى إكمال أو إلغاء:'
                    . ($activeCircles > 0 ? " {$activeCircles} حلقة صندوق" : '')
                    . ($pendingWeddings > 0 ? " {$pendingWeddings} تسجيل عرس" : ''),
            ], 422);
        }

        $recommender = Recommender::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'institution' => $request->institution,
            'bio' => $request->bio,
            'honor_pledge_signed' => true,
        ]);

        $user->role = 'recommender';
        $user->save();
        $user->syncRoles(['recommender']);

        // Revoke old tokens and issue a fresh one so the frontend gets updated role
        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيلك كمعرّف بنجاح. في انتظار اعتماد الإدارة',
            'user' => new UserResource($user->fresh()->load('city')),
            'token' => $token,
            'recommender' => $recommender,
        ], 201);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        // Admins always get an oversight view showing all candidates/recommendations,
        // regardless of whether they have a legacy Recommender record in the table.
        if ($user->role === 'admin') {
            return response()->json([
                'recommender' => [
                    'id' => null,
                    'type' => 'admin_oversight',
                    'is_approved' => true,
                    'candidates_count' => Candidate::count(),
                    'successful_matches' => Recommendation::where('status', 'accepted')->count(),
                ],
                'candidates' => Candidate::with('city:id,name')->latest()->take(20)->get(),
                'recommendations' => Recommendation::with([
                    'maleCandidate:id,name,age,occupation',
                    'femaleCandidate:id,name,age,occupation',
                ])->latest()->take(10)->get(),
            ]);
        }

        $recommender = Recommender::where('user_id', $user->id)->first();

        if (!$recommender) {
            return response()->json(['message' => 'لم يتم العثور على سجل معرّف'], 404);
        }

        $candidates = Candidate::where('recommender_id', $recommender->id)
            ->with('city:id,name')
            ->get();

        $recommendations = Recommendation::where('recommender_id', $recommender->id)
            ->with(['maleCandidate:id,name,age,occupation', 'femaleCandidate:id,name,age,occupation'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'recommender' => [
                'id' => $recommender->id,
                'type' => $recommender->type,
                'is_approved' => $recommender->is_approved,
                'candidates_count' => $recommender->candidates_count,
                'successful_matches' => $recommender->successful_matches,
            ],
            'candidates' => $candidates,
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * Resolve the current user's Recommender record or abort with 403.
     */
    private function resolveRecommender(Request $request): Recommender
    {
        $recommender = Recommender::where('user_id', $request->user()->id)->first();

        if (!$recommender) {
            abort(403, 'يجب التسجيل كمعرّف أولاً');
        }

        return $recommender;
    }

    public function addCandidate(StoreCandidateRequest $request): JsonResponse
    {
        $recommender = $this->resolveRecommender($request);

        if (!$recommender->is_approved) {
            return response()->json(['message' => 'حسابك كمعرّف لم يُعتمد بعد'], 403);
        }

        $candidate = Candidate::create([
            'recommender_id' => $recommender->id,
            'name' => $request->name,
            'gender' => $request->gender,
            'age' => $request->age,
            'education' => $request->education,
            'occupation' => $request->occupation,
            'city_id' => $request->city_id,
            'marital_status' => $request->marital_status,
            'religiosity_level' => $request->religiosity_level,
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
            'guardian_relation' => $request->guardian_relation,
            'preferences' => $request->preferences,
            'recommender_notes' => $request->recommender_notes,
        ]);

        $recommender->increment('candidates_count');

        return response()->json(['message' => 'تم إضافة المرشح بنجاح', 'candidate' => $candidate], 201);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $recommender = $this->resolveRecommender($request);

        $suggestions = $this->compatibilityService->generateSuggestions($recommender->id);

        return response()->json($suggestions);
    }

    public function recommend(StoreRecommendationRequest $request): JsonResponse
    {
        $recommender = $this->resolveRecommender($request);

        $male = Candidate::findOrFail($request->male_candidate_id);
        $female = Candidate::findOrFail($request->female_candidate_id);

        // Verify candidates belong to this recommender
        if ($male->recommender_id !== $recommender->id || $female->recommender_id !== $recommender->id) {
            return response()->json(['message' => 'هؤلاء المرشحون لا ينتمون لحسابك'], 403);
        }

        $recommendation = Recommendation::create([
            'recommender_id' => $recommender->id,
            'male_candidate_id' => $request->male_candidate_id,
            'female_candidate_id' => $request->female_candidate_id,
            'reason' => $request->reason,
            'compatibility_score' => $this->compatibilityService->calculateScore($male, $female),
        ]);

        return response()->json(['message' => 'تم إنشاء التوصية بنجاح', 'recommendation' => $recommendation], 201);
    }

    public function familyRequests(Request $request): JsonResponse
    {
        $recommender = $this->resolveRecommender($request);

        $requests = FamilyRequest::whereHas('recommendation', function ($q) use ($recommender) {
            $q->where('recommender_id', $recommender->id);
        })
            ->with(['recommendation.maleCandidate:id,name', 'recommendation.femaleCandidate:id,name'])
            ->latest()
            ->paginate(20);

        return response()->json($requests);
    }

    public function updateCandidate(Request $request, Candidate $candidate): JsonResponse
    {
        $recommender = $this->resolveRecommender($request);

        if ($candidate->recommender_id !== $recommender->id) {
            return response()->json(['message' => 'غير مصرح لك بتعديل هذا المرشح'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'age' => 'sometimes|integer|min:18|max:60',
            'education' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'city_id' => 'sometimes|exists:cities,id',
            'marital_status' => 'sometimes|in:single,divorced,widowed',
            'religiosity_level' => 'sometimes|in:committed,moderate,learning',
            'guardian_name' => 'sometimes|string|max:255',
            'guardian_phone' => 'sometimes|string|max:255',
            'guardian_relation' => 'sometimes|string|max:100',
            'preferences' => 'nullable|array',
            'recommender_notes' => 'nullable|string',
            'status' => 'sometimes|in:active,matched,withdrawn',
        ]);

        $candidate->update($request->only([
            'name', 'age', 'education', 'occupation', 'city_id',
            'marital_status', 'religiosity_level', 'guardian_name',
            'guardian_phone', 'guardian_relation', 'preferences',
            'recommender_notes', 'status',
        ]));

        return response()->json(['message' => 'تم تحديث بيانات المرشح', 'candidate' => $candidate->fresh()]);
    }

    public function respondToFamilyRequest(Request $request, FamilyRequest $familyRequest): JsonResponse
    {
        // Verify the family request belongs to this recommender
        $recommender = $this->resolveRecommender($request);
        if ($familyRequest->recommendation->recommender_id !== $recommender->id) {
            return response()->json(['message' => 'غير مصرح بهذا الإجراء'], 403);
        }

        $request->validate([
            'status' => 'required|in:accepted,rejected,meeting_scheduled',
            'meeting_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        $familyRequest->update([
            'status' => $request->status,
            'meeting_date' => $request->meeting_date,
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'تم تحديث الطلب بنجاح']);
    }
}
