<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Services\CostCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CalculatorController extends Controller
{
    public function __construct(
        private CostCalculatorService $calculatorService,
    ) {}

    public function cities(): JsonResponse
    {
        $cities = Cache::remember('cities:all', 86400, function () {
            return City::select('id', 'name', 'country', 'avg_marriage_cost', 'currency')
                ->orderBy('country')
                ->orderBy('name')
                ->get()
                ->toArray();
        });

        return response()->json($cities);
    }

    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'level' => 'required|in:simple,medium,luxury',
            'selected_items' => 'nullable|array',
            'selected_items.*' => 'integer|exists:cost_items,id',
        ]);

        $city = City::findOrFail($request->city_id);
        $result = $this->calculatorService->calculateDetailed(
            $city,
            $request->input('selected_items', []),
            $request->level
        );

        return response()->json($result);
    }

    /**
     * Get available cost items for customization
     */
    public function cityItems(Request $request): JsonResponse
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
        ]);

        $city = City::findOrFail($request->city_id);
        $items = $this->calculatorService->getCityItems($city);

        return response()->json($items);
    }
}
