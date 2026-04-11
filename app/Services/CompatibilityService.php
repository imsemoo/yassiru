<?php

namespace App\Services;

use App\Models\Candidate;

class CompatibilityService
{
    public function calculateScore(Candidate $male, Candidate $female): int
    {
        $score = 0;

        // Same city (+30)
        if ($male->city_id && $female->city_id && $male->city_id === $female->city_id) {
            $score += 30;
        }

        // Age compatibility: male older by 1-7 years (+25), 0-10 years (+15)
        $ageDiff = $male->age - $female->age;
        if ($ageDiff >= 1 && $ageDiff <= 7) {
            $score += 25;
        } elseif ($ageDiff >= 0 && $ageDiff <= 10) {
            $score += 15;
        }

        // Same religiosity level (+20)
        if ($male->religiosity_level === $female->religiosity_level) {
            $score += 20;
        }

        // Same marital status (+15)
        if ($male->marital_status === $female->marital_status) {
            $score += 15;
        }

        // Both have education (+10)
        if ($male->education && $female->education) {
            $score += 10;
        }

        return min($score, 100);
    }

    public function getMatchReasons(Candidate $male, Candidate $female): array
    {
        $reasons = [];

        if ($male->city_id && $female->city_id && $male->city_id === $female->city_id) {
            $reasons[] = 'نفس المدينة';
        }

        $ageDiff = $male->age - $female->age;
        if ($ageDiff >= 1 && $ageDiff <= 7) {
            $reasons[] = 'فارق عمر مناسب';
        }

        if ($male->religiosity_level === $female->religiosity_level) {
            $reasons[] = 'نفس مستوى التدين';
        }

        if ($male->marital_status === $female->marital_status) {
            $reasons[] = 'نفس الحالة الاجتماعية';
        }

        if ($male->education && $female->education) {
            $reasons[] = 'كلاهما حاصل على تعليم';
        }

        return $reasons;
    }

    public function generateSuggestions(int $recommenderId, int $minScore = 50, int $limit = 20): array
    {
        $maleCandidates = Candidate::where('recommender_id', $recommenderId)
            ->where('gender', 'male')
            ->where('status', 'active')
            ->get();

        // Limit female candidates to prevent DoS on large datasets
        $femaleCandidates = Candidate::where('gender', 'female')
            ->where('status', 'active')
            ->limit(500)
            ->get();

        $suggestions = [];

        foreach ($maleCandidates as $male) {
            foreach ($femaleCandidates as $female) {
                $score = $this->calculateScore($male, $female);
                if ($score >= $minScore) {
                    $suggestions[] = [
                        'male' => ['id' => $male->id, 'name' => $male->name, 'age' => $male->age, 'occupation' => $male->occupation],
                        'female' => ['id' => $female->id, 'name' => $female->name, 'age' => $female->age, 'occupation' => $female->occupation],
                        'score' => $score,
                        'reasons' => $this->getMatchReasons($male, $female),
                    ];
                }
            }
        }

        usort($suggestions, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($suggestions, 0, $limit);
    }
}
