<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CounselingSession;
use App\Models\FundCircle;
use App\Models\GroupWedding;
use App\Models\Recommendation;
use App\Models\User;
use App\Models\WeddingRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $stats = Cache::remember('platform:stats', 3600, function () {
            return [
                'users' => User::count(),
                'certified' => Certificate::count(),
                'circles' => FundCircle::whereIn('status', ['forming', 'active'])->count(),
                'marriages' => WeddingRegistration::where('payment_status', 'paid')->count(),
                'weddings' => GroupWedding::where('status', 'upcoming')->count(),
                'recommendations' => Recommendation::count(),
                'counseling_sessions' => CounselingSession::where('status', 'completed')->count(),
            ];
        });

        return response()->json($stats);
    }
}
