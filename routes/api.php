<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CalculatorController;
use App\Http\Controllers\Api\CounselingController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\FundController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RecommenderController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\WeddingController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth routes — strict rate limiting
Route::middleware('throttle:auth')->group(function () {
    Route::post('/auth/login', LoginController::class);
    Route::post('/auth/forgot-password', ForgotPasswordController::class);
    Route::post('/auth/reset-password', ResetPasswordController::class);
});

Route::middleware('throttle:registration')->group(function () {
    Route::post('/auth/register', RegisterController::class);
});

// Public routes — standard API rate limiting
Route::middleware('throttle:api')->group(function () {
    Route::get('/stats', StatsController::class);
    Route::get('/calculator/cities', [CalculatorController::class, 'cities']);
    Route::post('/calculator/calculate', [CalculatorController::class, 'calculate']);
    Route::get('/calculator/items', [CalculatorController::class, 'cityItems']);
    Route::get('/certificates/verify/{number}', [CourseController::class, 'verifyCertificate']);
    Route::get('/weddings', [WeddingController::class, 'index']);
    Route::get('/weddings/{wedding}', [WeddingController::class, 'show']);
    Route::get('/vendors', [WeddingController::class, 'vendors']);
});

// Payment webhook (no auth, no CSRF)
Route::post('/payments/webhook/fawry', [PaymentController::class, 'fawryWebhook']);
Route::get('/payment/callback', [PaymentController::class, 'callback']);

// Authenticated routes
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/auth/user', UserController::class);
    Route::put('/auth/user', function (UpdateProfileRequest $request) {
        $request->user()->update($request->validated());
        return response()->json(['message' => 'تم تحديث البيانات']);
    });
    Route::put('/auth/password', function (Request $request) {
        $request->validate([
            'current' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        if (!\Hash::check($request->current, $request->user()->password)) {
            return response()->json(['message' => 'كلمة المرور الحالية غير صحيحة'], 422);
        }
        $request->user()->password = \Hash::make($request->password);
        $request->user()->save();

        // Revoke all existing tokens (force re-login)
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'تم تغيير كلمة المرور. يرجى تسجيل الدخول مرة أخرى']);
    });
    Route::post('/auth/logout', LogoutController::class);
    Route::get('/auth/trust-score', function (Request $request) {
        $trustService = app(\App\Services\TrustScoreService::class);
        return response()->json($trustService->getProfile($request->user()));
    });

    // Identity verification
    Route::post('/auth/verify-identity', function (Request $request) {
        $request->validate([
            'national_id' => 'required|string|max:20',
            'national_id_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $path = $request->hasFile('national_id_image')
            ? $request->file('national_id_image')->store('verifications', 'local')
            : null;

        $service = app(\App\Services\IdentityVerificationService::class);
        $result = $service->verifyIdentity($request->user(), $request->national_id, $path);

        return response()->json(['message' => $result['message']], $result['success'] ? 200 : 422);
    });

    // Phone OTP
    Route::post('/auth/send-phone-otp', function (Request $request) {
        $service = app(\App\Services\IdentityVerificationService::class);
        $service->sendPhoneOtp($request->user());
        return response()->json(['message' => 'تم إرسال رمز التحقق']);
    });
    Route::post('/auth/verify-phone', function (Request $request) {
        $request->validate(['otp' => 'required|string|size:6']);
        $service = app(\App\Services\IdentityVerificationService::class);
        $result = $service->verifyPhoneOtp($request->user(), $request->otp);
        if ($result['success']) {
            $request->user()->update(['email_verified_at' => now()]); // phone verified
        }
        return response()->json(['message' => $result['message']], $result['success'] ? 200 : 422);
    });

    // Notifications
    Route::get('/notifications', function (Request $request) {
        return response()->json($request->user()->notifications()->latest()->take(20)->get());
    });
    Route::post('/notifications/read', function (Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'تم تعليم الإشعارات كمقروءة']);
    });
    Route::get('/notifications/unread-count', function (Request $request) {
        return response()->json(['count' => $request->user()->unreadNotifications()->count()]);
    });

    // ========================================================
    // USER-ONLY ROUTES (marriage-seeking features)
    // ========================================================
    Route::middleware('role:user|admin')->group(function () {
        // Courses
        Route::get('/courses', [CourseController::class, 'index']);
        Route::get('/courses/{course}', [CourseController::class, 'show']);
        Route::get('/courses/{course}/lessons/{lesson}', [CourseController::class, 'lesson']);
        Route::post('/courses/{course}/lessons/{lesson}/complete', [CourseController::class, 'completeLesson']);
        Route::post('/courses/{course}/lessons/{lesson}/progress', [CourseController::class, 'updateProgress']);
        Route::get('/courses/{course}/quiz', [CourseController::class, 'quiz']);
        Route::post('/courses/{course}/quiz', [CourseController::class, 'submitQuiz'])->middleware('throttle:quiz');
        Route::get('/certificate', [CourseController::class, 'certificate']);
        Route::get('/certificate/pdf', [CourseController::class, 'certificatePdf']);

        // Fund circles
        Route::prefix('circles')->group(function () {
            Route::get('/', [FundController::class, 'index']);
            Route::post('/', [FundController::class, 'store'])->middleware('throttle:circle-create');
            Route::get('/{circle}', [FundController::class, 'show']);
            Route::post('/{circle}/join', [FundController::class, 'join']);
            Route::get('/{circle}/dashboard', [FundController::class, 'dashboard']);
            Route::post('/{circle}/contribute', [FundController::class, 'contribute']);
            Route::get('/{circle}/contract', [FundController::class, 'getContract']);
            Route::post('/{circle}/contract/sign', [FundController::class, 'signContract']);
            Route::post('/{circle}/guarantor', [FundController::class, 'addGuarantor']);
            Route::post('/guarantors/{guarantor}/confirm', [FundController::class, 'confirmGuarantor']);
            Route::post('/{circle}/guarantee-fee', [FundController::class, 'payGuaranteeFee']);
        });

        // Wedding registration
        Route::post('/weddings/{wedding}/register', [WeddingController::class, 'register']);
        Route::get('/my-weddings', [WeddingController::class, 'myRegistrations']);
        Route::delete('/wedding-registrations/{registration}', [WeddingController::class, 'cancelRegistration']);
        Route::post('/wedding-registrations/{registration}/pay', [WeddingController::class, 'pay']);

        // Counseling (booking as client)
        Route::prefix('counseling')->group(function () {
            Route::get('/', [CounselingController::class, 'index']);
            Route::post('/', [CounselingController::class, 'store']);
            Route::get('/slots', [CounselingController::class, 'availableSlots']);
            Route::put('/{session}/cancel', [CounselingController::class, 'cancel']);
        });
    });

    // ========================================================
    // RECOMMENDER-ONLY ROUTES
    // ========================================================
    Route::prefix('recommender')->group(function () {
        // Register is open to any authenticated user (apply to become recommender)
        Route::post('/register', [RecommenderController::class, 'register']);

        // Rest requires recommender role
        Route::middleware('role:recommender|admin')->group(function () {
            Route::get('/dashboard', [RecommenderController::class, 'dashboard']);
            Route::post('/candidates', [RecommenderController::class, 'addCandidate']);
            Route::put('/candidates/{candidate}', [RecommenderController::class, 'updateCandidate']);
            Route::get('/suggestions', [RecommenderController::class, 'suggestions'])->middleware('throttle:suggestions');
            Route::post('/recommend', [RecommenderController::class, 'recommend']);
            Route::get('/family-requests', [RecommenderController::class, 'familyRequests']);
            Route::put('/family-requests/{familyRequest}', [RecommenderController::class, 'respondToFamilyRequest']);
        });
    });

    // ========================================================
    // COUNSELOR-ONLY ROUTES
    // ========================================================
    Route::middleware('role:counselor|admin')->prefix('counselor')->group(function () {
        Route::get('/sessions', [CounselingController::class, 'counselorSessions']);
        Route::put('/sessions/{session}/complete', [CounselingController::class, 'complete']);
    });

    // Community — all authenticated users can READ, but only users/admins can POST
    Route::prefix('community')->group(function () {
        Route::get('/posts', [\App\Http\Controllers\Api\CommunityController::class, 'index']);
        Route::post('/posts', [\App\Http\Controllers\Api\CommunityController::class, 'store'])
            ->middleware('role:user|admin');
    });

    // Reports
    Route::post('/reports', function (Request $request) {
        $request->validate([
            'reported_type' => 'required|in:user,recommender,candidate,other',
            'reported_id' => 'required|integer',
            'reason' => 'required|string|max:2000',
        ]);

        // Prevent self-reporting
        if ($request->reported_type === 'user' && (int) $request->reported_id === $request->user()->id) {
            return response()->json(['message' => 'لا يمكنك الإبلاغ عن نفسك'], 422);
        }

        // Prevent duplicate pending reports
        $exists = \App\Models\Report::where('reporter_id', $request->user()->id)
            ->where('reported_type', $request->reported_type)
            ->where('reported_id', $request->reported_id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'لديك بلاغ معلق بالفعل لهذا الشخص'], 422);
        }

        \App\Models\Report::create([
            'reporter_id' => $request->user()->id,
            'reported_type' => $request->reported_type,
            'reported_id' => $request->reported_id,
            'reason' => strip_tags($request->reason),
        ]);

        return response()->json(['message' => 'تم إرسال البلاغ بنجاح'], 201);
    })->middleware('throttle:reports');

    // Payments
    Route::prefix('payments')->group(function () {
        Route::post('/initiate', [PaymentController::class, 'initiate']);
        Route::get('/{payment:uuid}/status', [PaymentController::class, 'status']);
    });

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::get('/recommenders', [AdminController::class, 'recommenders']);
        Route::put('/recommenders/{recommender}', [AdminController::class, 'approveRecommender']);
        Route::get('/reports', [AdminController::class, 'reports']);
        Route::put('/reports/{report}', [AdminController::class, 'resolveReport']);

        Route::get('/weddings', [WeddingController::class, 'adminIndex']);
        Route::post('/weddings', [WeddingController::class, 'store']);
        Route::post('/vendors', [WeddingController::class, 'storeVendor']);

        Route::get('/counseling', [CounselingController::class, 'adminIndex']);
        Route::put('/counseling/{session}/complete', [CounselingController::class, 'complete']);

        // Audit logs
        Route::get('/audit-logs', [AdminController::class, 'auditLogs']);
    });
});
