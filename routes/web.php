<?php

use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

// Sitemap
Route::get('/sitemap.xml', function () {
    $urls = Cache::remember('sitemap', 86400, function () {
        $urls = collect([
            ['loc' => url('/'), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => url('/calculator'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => url('/courses'), 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => url('/weddings'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/fund'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => url('/register'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => url('/about'), 'priority' => '0.5', 'changefreq' => 'monthly'],
        ]);

        // Dynamic: courses
        \App\Models\Course::where('is_active', true)->each(function ($course) use ($urls) {
            $urls->push(['loc' => url("/courses/{$course->id}"), 'priority' => '0.7', 'changefreq' => 'monthly']);
        });

        // Dynamic: upcoming weddings
        \App\Models\GroupWedding::where('wedding_date', '>', now())->each(function ($wedding) use ($urls) {
            $urls->push(['loc' => url("/weddings/{$wedding->id}"), 'priority' => '0.6', 'changefreq' => 'weekly']);
        });

        return $urls;
    });

    return response()->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml');
});

// SPA catch-all — passes dynamic meta tags for crawlers
Route::get('/{any}', function (Request $request) {
    $meta = SeoService::getMetaForPath($request->path());
    return view('app', compact('meta'));
})->where('any', '.*');
