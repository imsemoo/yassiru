<?php

namespace App\Services;

use App\Models\City;
use App\Models\CostItem;

class CostCalculatorService
{
    // Fallback multipliers if no real data exists
    private const LEVEL_MULTIPLIERS = [
        'simple' => 0.6,
        'medium' => 1.0,
        'luxury' => 1.8,
    ];

    private const YASSIRU_DISCOUNT = 0.3;

    /**
     * Calculate using real itemized data from DB
     */
    public function calculateDetailed(City $city, array $selectedItems = [], string $level = 'medium'): array
    {
        $costItems = CostItem::where('city_id', $city->id)->orderBy('sort_order')->get();

        // If no real data, fallback to simple calculation
        if ($costItems->isEmpty()) {
            return $this->calculateSimple($city, $level);
        }

        $costField = match ($level) {
            'simple' => 'cost_min',
            'luxury' => 'cost_max',
            default => 'cost_avg',
        };

        $breakdown = [];
        $totalIndividual = 0;
        $totalYassiru = 0;

        foreach ($costItems as $item) {
            // If specific items selected, only include those; otherwise include required + all
            if (!empty($selectedItems) && !in_array($item->id, $selectedItems) && !$item->is_required) {
                continue;
            }

            $itemCost = $item->$costField;
            $yassiruCost = $item->yassiru_cost ?? $itemCost; // No yassiru option = same cost

            $totalIndividual += $itemCost;
            $totalYassiru += $yassiruCost;

            $savings = $itemCost - $yassiruCost;
            $savingsPercent = $itemCost > 0 ? round(($savings / $itemCost) * 100) : 0;

            $breakdown[] = [
                'id' => $item->id,
                'category' => $item->category,
                'label' => $item->label,
                'individual_cost' => round($itemCost),
                'yassiru_cost' => round($yassiruCost),
                'savings' => round($savings),
                'savings_percent' => $savingsPercent,
                'yassiru_note' => $item->yassiru_note,
                'is_required' => $item->is_required,
            ];
        }

        $totalSavings = $totalIndividual - $totalYassiru;
        $totalSavingsPercent = $totalIndividual > 0 ? round(($totalSavings / $totalIndividual) * 100) : 0;

        return [
            'city' => $city->name,
            'currency' => $city->currency,
            'level' => $level,
            'individual_cost' => round($totalIndividual),
            'yassiru_cost' => round($totalYassiru),
            'savings' => round($totalSavings),
            'savings_percent' => $totalSavingsPercent,
            'breakdown' => $breakdown,
            'data_source' => 'real', // Flag that this is real data
        ];
    }

    /**
     * Get available cost items for a city (for frontend customization)
     */
    public function getCityItems(City $city): array
    {
        return CostItem::where('city_id', $city->id)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category')
            ->map(fn($items, $category) => [
                'category' => $category,
                'label' => $this->categoryLabel($category),
                'items' => $items->map(fn($item) => [
                    'id' => $item->id,
                    'label' => $item->label,
                    'cost_min' => round($item->cost_min),
                    'cost_avg' => round($item->cost_avg),
                    'cost_max' => round($item->cost_max),
                    'yassiru_cost' => $item->yassiru_cost ? round($item->yassiru_cost) : null,
                    'yassiru_note' => $item->yassiru_note,
                    'is_required' => $item->is_required,
                ])->values(),
            ])
            ->values()
            ->toArray();
    }

    /**
     * Fallback simple calculation (old method)
     */
    public function calculateSimple(City $city, string $level): array
    {
        $multiplier = self::LEVEL_MULTIPLIERS[$level] ?? 1.0;
        $individualCost = $city->avg_marriage_cost * $multiplier;
        $yassiruCost = $individualCost * self::YASSIRU_DISCOUNT;
        $savings = $individualCost - $yassiruCost;

        return [
            'city' => $city->name,
            'currency' => $city->currency,
            'level' => $level,
            'individual_cost' => round($individualCost),
            'yassiru_cost' => round($yassiruCost),
            'savings' => round($savings),
            'savings_percent' => 70,
            'breakdown' => [],
            'data_source' => 'estimated',
        ];
    }

    /**
     * Legacy calculate method (backwards compatible)
     */
    public function calculate(City $city, string $level): array
    {
        return $this->calculateDetailed($city, [], $level);
    }

    private function categoryLabel(string $category): string
    {
        return match ($category) {
            'dowry' => 'المهر',
            'gold' => 'الذهب والمجوهرات',
            'venue' => 'القاعة والفرح',
            'furniture' => 'الأثاث والأجهزة',
            'housing' => 'السكن',
            'clothing' => 'الملابس',
            'other' => 'مصاريف أخرى',
            default => $category,
        };
    }
}
