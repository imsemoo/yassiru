<?php

namespace Tests\Unit;

use App\Models\Candidate;
use App\Services\CompatibilityService;
use PHPUnit\Framework\TestCase;

class CompatibilityServiceTest extends TestCase
{
    private CompatibilityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CompatibilityService();
    }

    private function makeCandidate(array $attrs = []): Candidate
    {
        $candidate = new Candidate();
        $candidate->forceFill(array_merge([
            'city_id' => 1,
            'age' => 28,
            'gender' => 'male',
            'religiosity_level' => 'committed',
            'marital_status' => 'single',
            'education' => 'جامعي',
        ], $attrs));

        return $candidate;
    }

    public function test_perfect_match_scores_100(): void
    {
        $male = $this->makeCandidate(['gender' => 'male', 'age' => 30]);
        $female = $this->makeCandidate(['gender' => 'female', 'age' => 25]);

        $score = $this->service->calculateScore($male, $female);

        // city(30) + age(25) + religiosity(20) + marital(15) + education(10) = 100
        $this->assertEquals(100, $score);
    }

    public function test_different_city_loses_30_points(): void
    {
        $male = $this->makeCandidate(['gender' => 'male', 'age' => 30, 'city_id' => 1]);
        $female = $this->makeCandidate(['gender' => 'female', 'age' => 25, 'city_id' => 2]);

        $score = $this->service->calculateScore($male, $female);

        // age(25) + religiosity(20) + marital(15) + education(10) = 70
        $this->assertEquals(70, $score);
    }

    public function test_age_diff_8_to_10_gives_15_points(): void
    {
        $male = $this->makeCandidate(['gender' => 'male', 'age' => 35]);
        $female = $this->makeCandidate(['gender' => 'female', 'age' => 26]);

        $score = $this->service->calculateScore($male, $female);

        // city(30) + age(15) + religiosity(20) + marital(15) + education(10) = 90
        $this->assertEquals(90, $score);
    }

    public function test_different_religiosity_loses_20_points(): void
    {
        $male = $this->makeCandidate(['gender' => 'male', 'age' => 30, 'religiosity_level' => 'committed']);
        $female = $this->makeCandidate(['gender' => 'female', 'age' => 25, 'religiosity_level' => 'moderate']);

        $score = $this->service->calculateScore($male, $female);

        // city(30) + age(25) + marital(15) + education(10) = 80
        $this->assertEquals(80, $score);
    }

    public function test_get_match_reasons_returns_reasons(): void
    {
        $male = $this->makeCandidate(['gender' => 'male', 'age' => 30]);
        $female = $this->makeCandidate(['gender' => 'female', 'age' => 25]);

        $reasons = $this->service->getMatchReasons($male, $female);

        $this->assertContains('نفس المدينة', $reasons);
        $this->assertContains('فارق عمر مناسب', $reasons);
        $this->assertContains('نفس مستوى التدين', $reasons);
        $this->assertContains('نفس الحالة الاجتماعية', $reasons);
    }

    public function test_score_capped_at_100(): void
    {
        $male = $this->makeCandidate(['gender' => 'male', 'age' => 30]);
        $female = $this->makeCandidate(['gender' => 'female', 'age' => 25]);

        $score = $this->service->calculateScore($male, $female);

        $this->assertLessThanOrEqual(100, $score);
    }

    public function test_no_match_with_large_age_diff(): void
    {
        $male = $this->makeCandidate(['gender' => 'male', 'age' => 25, 'city_id' => 1, 'religiosity_level' => 'moderate', 'marital_status' => 'divorced', 'education' => null]);
        $female = $this->makeCandidate(['gender' => 'female', 'age' => 40, 'city_id' => 2, 'religiosity_level' => 'committed', 'marital_status' => 'single', 'education' => null]);

        $score = $this->service->calculateScore($male, $female);

        // Nothing matches: 0
        $this->assertEquals(0, $score);
    }
}
