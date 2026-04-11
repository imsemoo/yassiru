<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'title' => 'المسار الشرعي',
                'track' => 'shariah',
                'description' => 'أحكام الزواج والحقوق الزوجية وفقه الخلافات من المنظور الشرعي',
                'duration_hours' => 3,
                'order_index' => 1,
                'lessons' => [
                    ['title' => 'الزواج في الإسلام', 'duration_minutes' => 45, 'order_index' => 1],
                    ['title' => 'حقوق الزوجين', 'duration_minutes' => 45, 'order_index' => 2],
                    ['title' => 'فقه الخلافات الزوجية', 'duration_minutes' => 45, 'order_index' => 3],
                    ['title' => 'العلاقة الحميمية في الإسلام', 'duration_minutes' => 45, 'order_index' => 4],
                ],
            ],
            [
                'title' => 'المسار النفسي',
                'track' => 'psychology',
                'description' => 'مهارات التواصل والذكاء العاطفي وإدارة التوقعات والضغوط',
                'duration_hours' => 3,
                'order_index' => 2,
                'lessons' => [
                    ['title' => 'التواصل الفعال', 'duration_minutes' => 45, 'order_index' => 1],
                    ['title' => 'إدارة التوقعات', 'duration_minutes' => 45, 'order_index' => 2],
                    ['title' => 'الذكاء العاطفي', 'duration_minutes' => 45, 'order_index' => 3],
                    ['title' => 'التعامل مع الضغوط', 'duration_minutes' => 45, 'order_index' => 4],
                ],
            ],
            [
                'title' => 'المسار المالي',
                'track' => 'financial',
                'description' => 'إعداد الميزانية والتخطيط المالي وتأسيس المنزل بأقل تكلفة',
                'duration_hours' => 2.5,
                'order_index' => 3,
                'lessons' => [
                    ['title' => 'ميزانية الأسرة من الصفر', 'duration_minutes' => 50, 'order_index' => 1],
                    ['title' => 'تأسيس المنزل بأقل تكلفة', 'duration_minutes' => 50, 'order_index' => 2],
                    ['title' => 'التخطيط المالي طويل المدى', 'duration_minutes' => 50, 'order_index' => 3],
                ],
            ],
            [
                'title' => 'المسار العملي',
                'track' => 'practical',
                'description' => 'إدارة المنزل والتعامل مع الأهل ودليل السنة الأولى',
                'duration_hours' => 2,
                'order_index' => 4,
                'lessons' => [
                    ['title' => 'إدارة المنزل', 'duration_minutes' => 40, 'order_index' => 1],
                    ['title' => 'التعامل مع الأهل', 'duration_minutes' => 40, 'order_index' => 2],
                    ['title' => 'دليل البقاء في السنة الأولى', 'duration_minutes' => 40, 'order_index' => 3],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $lessons = $courseData['lessons'];
            unset($courseData['lessons']);

            $courseData['lessons_count'] = count($lessons);
            $course = Course::create($courseData);

            foreach ($lessons as $lesson) {
                $course->lessons()->create(array_merge($lesson, [
                    'type' => 'video',
                ]));
            }
        }
    }
}
