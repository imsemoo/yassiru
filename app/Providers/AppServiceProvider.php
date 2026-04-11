<?php

namespace App\Providers;

use App\Contracts\PaymentProviderInterface;
use App\Services\Payment\Providers\FawryPaymentProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentProviderInterface::class, FawryPaymentProvider::class);
    }

    public function boot(): void
    {
        // API: 60 requests/minute per user
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Auth endpoints: 5 attempts/minute
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Registration: 3 per hour per IP
        RateLimiter::for('registration', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // Quiz submission: 3 per hour per user
        RateLimiter::for('quiz', function (Request $request) {
            return Limit::perHour(3)->by($request->user()?->id ?: $request->ip());
        });

        // Circle creation: 5 per hour per user
        RateLimiter::for('circle-create', function (Request $request) {
            return Limit::perHour(5)->by($request->user()?->id ?: $request->ip());
        });

        // Reports: 10 per hour per user
        RateLimiter::for('reports', function (Request $request) {
            return Limit::perHour(10)->by($request->user()?->id ?: $request->ip());
        });

        // Suggestions: 10 per minute per user (heavy computation)
        RateLimiter::for('suggestions', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
