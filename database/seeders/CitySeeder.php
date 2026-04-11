<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Realistic 2026 marriage cost data per city.
     *
     * Egyptian numbers based on:
     * - Gold price 21k = 7,210 EGP/gram (April 2026)
     * - Middle-class wedding total = 700K EGP without apartment (Arabian Business)
     * - Mansoura/Dakahlia known for قوائم منقولات reaching 1M+ EGP
     *
     * Gulf numbers based on industry surveys (2026):
     * - Riyadh: 150-400K SAR average (Midan Al-Mal)
     * - Dubai: 150-500K AED average (Easy Wedding Dubai)
     * - Kuwait: ~25K KWD average
     */
    public function run(): void
    {
        $now = now();

        DB::table('cities')->insert([
            // === مصر === (Mansoura intentionally HIGHER than Cairo due to منقولات pressure)
            ['name' => 'القاهرة', 'country' => 'مصر', 'country_code' => 'EG', 'avg_marriage_cost' => 700000, 'currency' => 'EGP', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'الإسكندرية', 'country' => 'مصر', 'country_code' => 'EG', 'avg_marriage_cost' => 600000, 'currency' => 'EGP', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'المنصورة', 'country' => 'مصر', 'country_code' => 'EG', 'avg_marriage_cost' => 1100000, 'currency' => 'EGP', 'created_at' => $now, 'updated_at' => $now],

            // === السعودية ===
            ['name' => 'الرياض', 'country' => 'السعودية', 'country_code' => 'SA', 'avg_marriage_cost' => 390000, 'currency' => 'SAR', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'جدة', 'country' => 'السعودية', 'country_code' => 'SA', 'avg_marriage_cost' => 320000, 'currency' => 'SAR', 'created_at' => $now, 'updated_at' => $now],

            // === الإمارات ===
            ['name' => 'دبي', 'country' => 'الإمارات', 'country_code' => 'AE', 'avg_marriage_cost' => 525000, 'currency' => 'AED', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'أبوظبي', 'country' => 'الإمارات', 'country_code' => 'AE', 'avg_marriage_cost' => 480000, 'currency' => 'AED', 'created_at' => $now, 'updated_at' => $now],

            // === باقي الخليج والشام ===
            ['name' => 'الكويت', 'country' => 'الكويت', 'country_code' => 'KW', 'avg_marriage_cost' => 28000, 'currency' => 'KWD', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'عمّان', 'country' => 'الأردن', 'country_code' => 'JO', 'avg_marriage_cost' => 55000, 'currency' => 'JOD', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'بيروت', 'country' => 'لبنان', 'country_code' => 'LB', 'avg_marriage_cost' => 25000, 'currency' => 'USD', 'created_at' => $now, 'updated_at' => $now],

            // === المغرب العربي ===
            ['name' => 'الدار البيضاء', 'country' => 'المغرب', 'country_code' => 'MA', 'avg_marriage_cost' => 150000, 'currency' => 'MAD', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'تونس', 'country' => 'تونس', 'country_code' => 'TN', 'avg_marriage_cost' => 50000, 'currency' => 'TND', 'created_at' => $now, 'updated_at' => $now],

            // === تركيا والعراق والسودان ===
            ['name' => 'إسطنبول', 'country' => 'تركيا', 'country_code' => 'TR', 'avg_marriage_cost' => 350000, 'currency' => 'TRY', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'الخرطوم', 'country' => 'السودان', 'country_code' => 'SD', 'avg_marriage_cost' => 8000000, 'currency' => 'SDG', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'بغداد', 'country' => 'العراق', 'country_code' => 'IQ', 'avg_marriage_cost' => 25000000, 'currency' => 'IQD', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
