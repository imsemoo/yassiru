<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\CostItem;
use Illuminate\Database\Seeder;

class CostItemSeeder extends Seeder
{
    public function run(): void
    {
        $cairo = City::where('name', 'القاهرة')->first();
        $alex = City::where('name', 'الإسكندرية')->first();
        $riyadh = City::where('name', 'الرياض')->first();
        $jeddah = City::where('name', 'جدة')->first();

        if ($cairo) $this->seedCity($cairo->id, 'EGP', $this->cairoItems());
        if ($alex) $this->seedCity($alex->id, 'EGP', $this->alexItems());
        if ($riyadh) $this->seedCity($riyadh->id, 'SAR', $this->riyadhItems());
        if ($jeddah) $this->seedCity($jeddah->id, 'SAR', $this->jeddahItems());
    }

    private function seedCity(int $cityId, string $currency, array $items): void
    {
        foreach ($items as $i => $item) {
            CostItem::create(array_merge($item, [
                'city_id' => $cityId,
                'sort_order' => $i + 1,
            ]));
        }
    }

    private function cairoItems(): array
    {
        return [
            ['category' => 'dowry', 'label' => 'المهر (الشبكة)', 'cost_min' => 15000, 'cost_avg' => 30000, 'cost_max' => 80000, 'yassiru_cost' => null, 'yassiru_note' => 'حسب الاتفاق بين العائلتين — المنصة تشجع التيسير', 'is_required' => true],
            ['category' => 'gold', 'label' => 'الذهب والمجوهرات', 'cost_min' => 10000, 'cost_avg' => 25000, 'cost_max' => 100000, 'yassiru_cost' => null, 'yassiru_note' => 'خصومات شركاء المنصة على الذهب 5-10%', 'is_required' => false],
            ['category' => 'venue', 'label' => 'قاعة الفرح', 'cost_min' => 8000, 'cost_avg' => 20000, 'cost_max' => 80000, 'yassiru_cost' => 3000, 'yassiru_note' => 'عرس جماعي: 3,000 بدل 20,000 — توفير 85%', 'is_required' => true],
            ['category' => 'furniture', 'label' => 'الأثاث والأجهزة', 'cost_min' => 30000, 'cost_avg' => 80000, 'cost_max' => 250000, 'yassiru_cost' => 35000, 'yassiru_note' => 'أثاث مستعمل نظيف عبر شركاء المنصة — توفير 55%', 'is_required' => true],
            ['category' => 'housing', 'label' => 'إيجار شقة (سنة)', 'cost_min' => 18000, 'cost_avg' => 36000, 'cost_max' => 96000, 'yassiru_cost' => 18000, 'yassiru_note' => 'بدء بسيط في منطقة مناسبة', 'is_required' => true],
            ['category' => 'clothing', 'label' => 'ملابس الفرح', 'cost_min' => 3000, 'cost_avg' => 8000, 'cost_max' => 30000, 'yassiru_cost' => 3000, 'yassiru_note' => 'خصومات شركاء 40-60%', 'is_required' => true],
            ['category' => 'other', 'label' => 'تصوير وتجهيزات', 'cost_min' => 2000, 'cost_avg' => 7000, 'cost_max' => 25000, 'yassiru_cost' => 0, 'yassiru_note' => 'مشمول في باقة العرس الجماعي', 'is_required' => false],
            ['category' => 'other', 'label' => 'مصاريف إدارية ومتنوعة', 'cost_min' => 2000, 'cost_avg' => 5000, 'cost_max' => 15000, 'yassiru_cost' => 2000, 'yassiru_note' => null, 'is_required' => false],
        ];
    }

    private function alexItems(): array
    {
        return [
            ['category' => 'dowry', 'label' => 'المهر (الشبكة)', 'cost_min' => 10000, 'cost_avg' => 20000, 'cost_max' => 60000, 'yassiru_cost' => null, 'yassiru_note' => 'حسب الاتفاق — تكلفة أقل من القاهرة', 'is_required' => true],
            ['category' => 'gold', 'label' => 'الذهب والمجوهرات', 'cost_min' => 8000, 'cost_avg' => 20000, 'cost_max' => 70000, 'yassiru_cost' => null, 'yassiru_note' => 'خصومات شركاء المنصة 5-10%', 'is_required' => false],
            ['category' => 'venue', 'label' => 'قاعة الفرح', 'cost_min' => 5000, 'cost_avg' => 15000, 'cost_max' => 50000, 'yassiru_cost' => 2500, 'yassiru_note' => 'عرس جماعي: 2,500 بدل 15,000', 'is_required' => true],
            ['category' => 'furniture', 'label' => 'الأثاث والأجهزة', 'cost_min' => 25000, 'cost_avg' => 60000, 'cost_max' => 180000, 'yassiru_cost' => 28000, 'yassiru_note' => 'أثاث مستعمل نظيف — توفير 53%', 'is_required' => true],
            ['category' => 'housing', 'label' => 'إيجار شقة (سنة)', 'cost_min' => 12000, 'cost_avg' => 24000, 'cost_max' => 72000, 'yassiru_cost' => 14000, 'yassiru_note' => 'بدء بسيط في منطقة مناسبة', 'is_required' => true],
            ['category' => 'clothing', 'label' => 'ملابس الفرح', 'cost_min' => 2000, 'cost_avg' => 6000, 'cost_max' => 20000, 'yassiru_cost' => 2500, 'yassiru_note' => 'خصومات شركاء 40-60%', 'is_required' => true],
            ['category' => 'other', 'label' => 'تصوير ومتنوعات', 'cost_min' => 1500, 'cost_avg' => 5000, 'cost_max' => 15000, 'yassiru_cost' => 0, 'yassiru_note' => 'مشمول في العرس الجماعي', 'is_required' => false],
        ];
    }

    private function riyadhItems(): array
    {
        return [
            ['category' => 'dowry', 'label' => 'المهر', 'cost_min' => 20000, 'cost_avg' => 50000, 'cost_max' => 150000, 'yassiru_cost' => null, 'yassiru_note' => 'حسب الاتفاق بين العائلتين', 'is_required' => true],
            ['category' => 'gold', 'label' => 'الذهب والمجوهرات', 'cost_min' => 15000, 'cost_avg' => 40000, 'cost_max' => 200000, 'yassiru_cost' => null, 'yassiru_note' => 'خصومات شركاء 5-10%', 'is_required' => false],
            ['category' => 'venue', 'label' => 'قاعة/استراحة', 'cost_min' => 10000, 'cost_avg' => 35000, 'cost_max' => 150000, 'yassiru_cost' => 5000, 'yassiru_note' => 'عرس جماعي: 5,000 بدل 35,000', 'is_required' => true],
            ['category' => 'furniture', 'label' => 'الأثاث والأجهزة', 'cost_min' => 20000, 'cost_avg' => 50000, 'cost_max' => 200000, 'yassiru_cost' => 25000, 'yassiru_note' => 'أثاث عبر شركاء بخصومات 50%', 'is_required' => true],
            ['category' => 'housing', 'label' => 'إيجار شقة (سنة)', 'cost_min' => 15000, 'cost_avg' => 30000, 'cost_max' => 80000, 'yassiru_cost' => 18000, 'yassiru_note' => null, 'is_required' => true],
            ['category' => 'clothing', 'label' => 'ملابس وبشت العريس', 'cost_min' => 3000, 'cost_avg' => 10000, 'cost_max' => 40000, 'yassiru_cost' => 4000, 'yassiru_note' => 'خصومات شركاء', 'is_required' => true],
            ['category' => 'other', 'label' => 'تصوير وزفة وبوفيه', 'cost_min' => 5000, 'cost_avg' => 20000, 'cost_max' => 80000, 'yassiru_cost' => 0, 'yassiru_note' => 'مشمول في العرس الجماعي', 'is_required' => false],
        ];
    }

    private function jeddahItems(): array
    {
        return [
            ['category' => 'dowry', 'label' => 'المهر', 'cost_min' => 15000, 'cost_avg' => 40000, 'cost_max' => 120000, 'yassiru_cost' => null, 'yassiru_note' => 'حسب الاتفاق', 'is_required' => true],
            ['category' => 'gold', 'label' => 'الذهب', 'cost_min' => 10000, 'cost_avg' => 30000, 'cost_max' => 150000, 'yassiru_cost' => null, 'yassiru_note' => 'خصومات 5-10%', 'is_required' => false],
            ['category' => 'venue', 'label' => 'قاعة/استراحة', 'cost_min' => 8000, 'cost_avg' => 30000, 'cost_max' => 120000, 'yassiru_cost' => 4000, 'yassiru_note' => 'عرس جماعي', 'is_required' => true],
            ['category' => 'furniture', 'label' => 'الأثاث', 'cost_min' => 18000, 'cost_avg' => 45000, 'cost_max' => 180000, 'yassiru_cost' => 22000, 'yassiru_note' => 'أثاث بخصومات 50%', 'is_required' => true],
            ['category' => 'housing', 'label' => 'إيجار سنة', 'cost_min' => 12000, 'cost_avg' => 25000, 'cost_max' => 70000, 'yassiru_cost' => 15000, 'yassiru_note' => null, 'is_required' => true],
            ['category' => 'clothing', 'label' => 'ملابس الفرح', 'cost_min' => 2000, 'cost_avg' => 8000, 'cost_max' => 30000, 'yassiru_cost' => 3000, 'yassiru_note' => 'خصومات شركاء', 'is_required' => true],
        ];
    }
}
