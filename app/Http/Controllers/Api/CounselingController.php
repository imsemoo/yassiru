<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCounselingRequest;
use App\Models\CounselingSession;
use App\Services\CounselingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CounselingController extends Controller
{
    public function __construct(
        private CounselingService $counselingService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $sessions = CounselingSession::where('user_id', $request->user()->id)
            ->orderByDesc('scheduled_at')
            ->paginate(15);

        return response()->json($sessions);
    }

    public function store(StoreCounselingRequest $request): JsonResponse
    {
        if ($this->counselingService->hasConflict($request->user()->id, $request->scheduled_at)) {
            return response()->json(['message' => 'لديك جلسة محجوزة في هذا اليوم بالفعل'], 422);
        }

        $session = CounselingSession::create([
            'user_id' => $request->user()->id,
            'type' => $request->type,
            'scheduled_at' => $request->scheduled_at,
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'تم حجز الجلسة بنجاح', 'session' => $session], 201);
    }

    public function cancel(Request $request, CounselingSession $session): JsonResponse
    {
        Gate::authorize('cancel', $session);

        $session->update(['status' => 'cancelled']);

        return response()->json(['message' => 'تم إلغاء الجلسة']);
    }

    public function availableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'type' => 'required|in:individual,group',
        ]);

        $slots = $this->counselingService->getAvailableSlots($request->date);

        return response()->json($slots);
    }

    /**
     * List sessions assigned to the authenticated counselor.
     * For now, counselors see all scheduled/upcoming sessions (as a booking pool).
     * Future: add a counselor_id field and filter by it.
     */
    public function counselorSessions(Request $request): JsonResponse
    {
        $query = CounselingSession::with('user:id,name,email,phone')
            ->orderBy('scheduled_at', 'asc');

        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['scheduled', 'in_progress']);
        }

        return response()->json($query->paginate(20));
    }

    // Admin
    public function adminIndex(Request $request): JsonResponse
    {
        $query = CounselingSession::with('user:id,name,email,phone');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $sessions = $query->orderByDesc('scheduled_at')->paginate(20);

        return response()->json($sessions);
    }

    public function complete(CounselingSession $session): JsonResponse
    {
        $session->update(['status' => 'completed']);

        return response()->json(['message' => 'تم تسجيل إتمام الجلسة']);
    }
}
