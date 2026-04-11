<?php

namespace Tests\Unit;

use App\Models\City;
use App\Services\CostCalculatorService;
use PHPUnit\Framework\TestCase;

class CostCalculatorServiceTest extends TestCase
{
    private CostCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CostCalculatorService();
    }

    private function makeCity(float $avgCost = 150000, string $currency = 'EGP'): City
    {
        $city = new City();
        $city->forceFill([
            'name' => 'القاهرة',
            'avg_marriage_cost' => $avgCost,
            'currency' => $currency,
        ]);

        return $city;
    }

    public function test_simple_level_applies_0_6_multiplier(): void
    {
        $city = $this->makeCity(100000);
        $result = $this->service->calculateSimple($city, 'simple');

        $this->assertEquals(60000, $result['individual_cost']);
    }

    public function test_medium_level_applies_1_0_multiplier(): void
    {
        $city = $this->makeCity(100000);
        $result = $this->service->calculateSimple($city, 'medium');

        $this->assertEquals(100000, $result['individual_cost']);
    }

    public function test_luxury_level_applies_1_8_multiplier(): void
    {
        $city = $this->makeCity(100000);
        $result = $this->service->calculateSimple($city, 'luxury');

        $this->assertEquals(180000, $result['individual_cost']);
    }

    public function test_yassiru_cost_is_30_percent(): void
    {
        $city = $this->makeCity(100000);
        $result = $this->service->calculateSimple($city, 'medium');

        $this->assertEquals(30000, $result['yassiru_cost']);
        $this->assertEquals(70000, $result['savings']);
        $this->assertEquals(70, $result['savings_percent']);
    }

    public function test_simple_calculation_returns_empty_breakdown(): void
    {
        $city = $this->makeCity(100000);
        $result = $this->service->calculateSimple($city, 'medium');

        $this->assertIsArray($result['breakdown']);
        $this->assertEquals('estimated', $result['data_source']);
    }

    public function test_savings_percent_is_70(): void
    {
        $city = $this->makeCity(100000);
        $result = $this->service->calculateSimple($city, 'medium');

        $this->assertEquals(70, $result['savings_percent']);
    }

    public function test_returns_city_info(): void
    {
        $city = $this->makeCity(100000, 'SAR');
        $result = $this->service->calculateSimple($city, 'medium');

        $this->assertEquals('القاهرة', $result['city']);
        $this->assertEquals('SAR', $result['currency']);
        $this->assertEquals('medium', $result['level']);
    }
}
