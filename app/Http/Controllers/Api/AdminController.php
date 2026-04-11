<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\FundCircle;
use App\Models\Recommender;
use App\Models\Report;
use App\Models\User;
use App\Notifications\RecommenderApproved;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'users_count' => User::count(),
            'recommenders_count' => Recommender::count(),
            'pending_recommenders' => Recommender::where('is_approved', false)->count(),
            'certificates_count' => Certificate::count(),
            'circles_count' => FundCircle::whereIn('status', ['forming', 'active'])->count(),
            'reports_count' => Report::where('status', 'pending')->count(),
            'recent_users' => User::latest()->take(5)->get(['id', 'name', 'email', 'role', 'created_at']),
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        $users = User::with('city:id,name')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
            ->when($request->role, fn($q, $r) => $q->where('role', $r))
            ->latest()
            ->paginate(20);

        return response()->json($users);
    }

    public function recommenders(): JsonResponse
    {
        $recommenders = Recommender::with('user:id,name,email,phone')
            ->latest()
            ->paginate(20);

        return response()->json($recommenders);
    }

    public function approveRecommender(Request $request, Recommender $recommender): JsonResponse
    {
        $request->validate(['approved' => 'required|boolean']);

        $recommender->update([
            'is_approved' => $request->approved,
            'approved_at' => $request->approved ? now() : null,
        ]);

        $recommender->user->notify(new RecommenderApproved($request->approved));

        AuditService::log(
            $request->approved ? 'recommender_approved' : 'recommender_rejected',
            $recommender
        );

        return response()->json([
            'message' => $request->approved ? 'تم اعتماد المعرّف' : 'تم رفض المعرّف',
        ]);
    }

    public function reports(): JsonResponse
    {
        $reports = Report::with('reporter:id,name')
            ->latest()
            ->paginate(20);

        return response()->json($reports);
    }

    public function resolveReport(Request $request, Report $report): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:investigating,resolved,dismissed',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $oldStatus = $report->status;
        $report->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        AuditService::log('report_resolved', $report, ['status' => $oldStatus], ['status' => $request->status]);

        return response()->json(['message' => 'تم تحديث البلاغ']);
    }

    public function auditLogs(Request $request): JsonResponse
    {
        $query = AuditLog::with('user:id,name,email');

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->from) {
            $query->where('created_at', '>=', $request->from);
        }

        if ($request->to) {
            $query->where('created_at', '<=', $request->to);
        }

        $logs = $query->orderByDesc('created_at')->paginate(30);

        return response()->json($logs);
    }
}
