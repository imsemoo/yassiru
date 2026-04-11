<?php

namespace Database\Seeders;

use App\Models\GroupWedding;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class WeddingSeeder extends Seeder
{
    public function run(): void
    {
        // Group Weddings
        $weddings = [
            ['city_id' => 1, 'title' => 'العرس الجماعي الأول - القاهرة', 'venue_name' => 'قاعة النيل الكبرى', 'wedding_date' => '2026-06-15', 'max_grooms' => 20, 'price_per_groom' => 15000, 'original_price' => 45000, 'description' => 'عرس جماعي في قلب القاهرة يشمل القاعة والضيافة والتصوير', 'registration_deadline' => '2026-05-15'],
            ['city_id' => 2, 'title' => 'العرس الجماعي - الإسكندرية', 'venue_name' => 'فندق البحر المتوسط', 'wedding_date' => '2026-07-01', 'max_grooms' => 15, 'price_per_groom' => 12000, 'original_price' => 35000, 'description' => 'عرس على شاطئ الإسكندرية مع إطلالة بحرية', 'registration_deadline' => '2026-06-01'],
            ['city_id' => 3, 'title' => 'العرس الجماعي - الرياض', 'venue_name' => 'قاعة الملك فهد', 'wedding_date' => '2026-08-10', 'max_grooms' => 25, 'price_per_groom' => 25000, 'original_price' => 80000, 'description' => 'عرس جماعي فاخر في الرياض يشمل جميع الخدمات', 'registration_deadline' => '2026-07-10'],
            ['city_id' => 5, 'title' => 'العرس الجماعي - جدة', 'venue_name' => 'قاعة البحر الأحمر', 'wedding_date' => '2026-09-20', 'max_grooms' => 20, 'price_per_groom' => 22000, 'original_price' => 70000, 'description' => 'عرس مميز في جدة بإطلالة على البحر', 'registration_deadline' => '2026-08-20'],
            ['city_id' => 7, 'title' => 'العرس الجماعي - عمّان', 'venue_name' => 'فندق الأردن الدولي', 'wedding_date' => '2026-10-05', 'max_grooms' => 15, 'price_per_groom' => 8000, 'original_price' => 25000, 'description' => 'عرس جماعي اقتصادي في عمّان', 'registration_deadline' => '2026-09-05'],
        ];

        foreach ($weddings as $wedding) {
            GroupWedding::create($wedding);
        }

        // Vendors
        $vendors = [
            ['name' => 'استديو النور للتصوير', 'category' => 'photography', 'city_id' => 1, 'description' => 'تصوير فوتو وفيديو احترافي', 'discount_percent' => 30, 'contact_phone' => '+201001234567'],
            ['name' => 'قاعات الياسمين', 'category' => 'venue', 'city_id' => 1, 'description' => 'قاعات أفراح بسعة 200-500 ضيف', 'discount_percent' => 25, 'contact_phone' => '+201009876543'],
            ['name' => 'مطابخ البركة', 'category' => 'catering', 'city_id' => 1, 'description' => 'بوفيه مفتوح وضيافة فاخرة', 'discount_percent' => 20, 'contact_phone' => '+201005555555'],
            ['name' => 'الأثاث العصري', 'category' => 'furniture', 'city_id' => 1, 'description' => 'تأجير وتجهيز الأثاث بأسعار مخفضة', 'discount_percent' => 35, 'contact_phone' => '+201007777777'],
            ['name' => 'استديو الرؤية - الرياض', 'category' => 'photography', 'city_id' => 3, 'description' => 'تصوير وإخراج فني', 'discount_percent' => 25, 'contact_phone' => '+966501234567'],
            ['name' => 'ضيافة الكرم', 'category' => 'catering', 'city_id' => 3, 'description' => 'ضيافة عربية أصيلة', 'discount_percent' => 20, 'contact_phone' => '+966509876543'],
            ['name' => 'أزياء العروسة', 'category' => 'clothing', 'city_id' => 1, 'description' => 'فساتين وبدل بأسعار خاصة لمنصة يسّرو', 'discount_percent' => 40, 'contact_phone' => '+201008888888'],
            ['name' => 'قاعة اللؤلؤة - جدة', 'category' => 'venue', 'city_id' => 5, 'description' => 'قاعة فاخرة بإطلالة بحرية', 'discount_percent' => 20, 'contact_phone' => '+966555123456'],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }
    }
}
