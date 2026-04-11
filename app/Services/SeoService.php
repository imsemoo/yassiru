<?php

namespace App\Services;

use App\Models\Course;
use App\Models\GroupWedding;
use Illuminate\Support\Facades\Cache;

class SeoService
{
    /**
     * Get meta tags based on URL path for server-side rendering to crawlers.
     */
    public static function getMetaForPath(string $path): array
    {
        $default = [
            'title' => 'يسّرو — منصة تيسير الزواج الأولى في العالم الإسلامي',
            'description' => 'يسّرو تُيسّر زواجك من الألف للياء: دورة تأهيلية، توفيق شرعي عبر معرّفين موثوقين، صندوق تعاوني بدون فوائد، أعراس جماعية بتوفير 60%، ورعاية ما بعد الزواج.',
            'og_image' => '/logo.svg',
        ];

        // Static routes
        $staticMeta = [
            'calculator' => [
                'title' => 'حاسبة تكاليف الزواج | يسّرو',
                'description' => 'احسب تكلفة زواجك بالتفصيل وقارن بين الزواج التقليدي والعرس الجماعي. وفّر حتى 70% من التكاليف.',
            ],
            'courses' => [
                'title' => 'الدورة التأهيلية | يسّرو',
                'description' => 'أكمل الدورة التأهيلية بمساراتها الأربعة: الشرعي، النفسي، المالي، والعملي. احصل على شهادة الجاهزية الزواجية.',
            ],
            'weddings' => [
                'title' => 'الأعراس الجماعية | يسّرو',
                'description' => 'سجّل في أعراس جماعية منظمة بتوفير 60-70% من التكاليف. أعراس كريمة بأسعار ميسّرة.',
            ],
            'fund' => [
                'title' => 'الصندوق التعاوني | يسّرو',
                'description' => 'انضم لحلقات الصندوق التعاوني — جمعية رقمية بدون فوائد لتمويل زواجك بالتقسيط الحلال.',
            ],
            'counseling' => [
                'title' => 'الاستشارات | يسّرو',
                'description' => 'جلسات استشارية مع متخصصين في العلاقات الزوجية — دعم نفسي وشرعي لحياة زوجية مستقرة.',
            ],
            'login' => [
                'title' => 'تسجيل الدخول | يسّرو',
                'description' => 'سجّل دخولك لمنصة يسّرو وأكمل رحلتك نحو زواج ميسّر.',
            ],
            'register' => [
                'title' => 'إنشاء حساب | يسّرو',
                'description' => 'أنشئ حسابك المجاني في يسّرو وابدأ رحلتك: دورة تأهيلية، توفيق شرعي، وتمويل بدون فوائد.',
            ],
            'about' => [
                'title' => 'عن المنصة | يسّرو',
                'description' => 'يسّرو منظومة متكاملة لتيسير الزواج: تأهيل، توفيق شرعي، صندوق تعاوني، أعراس جماعية، ورعاية ما بعد الزواج.',
            ],
        ];

        // Check static routes first
        $normalizedPath = trim($path, '/');

        if (isset($staticMeta[$normalizedPath])) {
            return array_merge($default, $staticMeta[$normalizedPath]);
        }

        // Dynamic routes
        if (preg_match('/^courses\/(\d+)$/', $normalizedPath, $matches)) {
            return self::getCourseMetadata((int) $matches[1], $default);
        }

        if (preg_match('/^weddings\/(\d+)$/', $normalizedPath, $matches)) {
            return self::getWeddingMetadata((int) $matches[1], $default);
        }

        return $default;
    }

    private static function getCourseMetadata(int $id, array $default): array
    {
        return Cache::remember("seo:course:{$id}", 3600, function () use ($id, $default) {
            $course = Course::find($id);

            if (!$course) {
                return $default;
            }

            return [
                'title' => "{$course->title} | يسّرو",
                'description' => mb_substr($course->description, 0, 160),
                'og_image' => $default['og_image'],
            ];
        });
    }

    private static function getWeddingMetadata(int $id, array $default): array
    {
        return Cache::remember("seo:wedding:{$id}", 3600, function () use ($id, $default) {
            $wedding = GroupWedding::find($id);

            if (!$wedding) {
                return $default;
            }

            return [
                'title' => "{$wedding->title} | يسّرو",
                'description' => "عرس جماعي في {$wedding->venue_name} — سجّل الآن بتكلفة {$wedding->price_per_groom} فقط.",
                'og_image' => $default['og_image'],
            ];
        });
    }
}
