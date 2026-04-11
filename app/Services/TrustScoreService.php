<?php

namespace App\Services;

use App\Models\User;

class TrustScoreService
{
    // Point values for actions
    public const POINTS = [
        'identity_verified'       => 10,
        'course_completed'        => 10,
        'certificate_earned'      => 20,
        'circle_completed'        => 30,
        'monthly_payment_on_time' => 5,
        'recommender_approved'    => 15,
        'successful_match'        => 25,
        'counseling_attended'     => 5,
        'late_payment'            => -20,
        'frozen_in_circle'        => -50,
        'removed_from_circle'     => -100,
        'confirmed_report'        => -75,
        'false_report_filed'      => -30,
        'account_age_per_month'   => 2,
    ];

    // Trust levels
    public const LEVELS = [
        ['min' => 0,   'max' => 29,  'name' => 'جديد',      'level' => 'new'],
        ['min' => 30,  'max' => 59,  'name' => 'مبتدئ',     'level' => 'beginner'],
        ['min' => 60,  'max' => 99,  'name' => 'موثوق',     'level' => 'trusted'],
        ['min' => 100, 'max' => 199, 'name' => 'متميز',     'level' => 'distinguished'],
        ['min' => 200, 'max' => 99999, 'name' => 'رائد',    'level' => 'leader'],
    ];

    /**
     * Add points to user's trust score
     */
    public function addPoints(User $user, string $action, ?string $reason = null): int
    {
        $points = self::POINTS[$action] ?? 0;
        if ($points === 0) return $user->trust_score;

        $newScore = max(0, $user->trust_score + $points);
        $user->update(['trust_score' => $newScore]);

        AuditService::log('trust_score_changed', $user, [
            'old_score' => $user->trust_score - $points,
        ], [
            'new_score' => $newScore,
            'action' => $action,
            'points' => $points,
            'reason' => $reason,
        ]);

        return $newScore;
    }

    /**
     * Get user's trust level
     */
    public function getLevel(User $user): array
    {
        foreach (self::LEVELS as $level) {
            if ($user->trust_score >= $level['min'] && $user->trust_score <= $level['max']) {
                return $level;
            }
        }

        return self::LEVELS[0];
    }

    /**
     * Get user's trust profile
     */
    public function getProfile(User $user): array
    {
        $level = $this->getLevel($user);

        return [
            'score' => $user->trust_score,
            'level' => $level['level'],
            'level_name' => $level['name'],
            'max_circles' => $this->getAllowedCircles($user),
            'can_early_payout' => $user->trust_score >= 100,
            'benefits' => $this->getBenefits($user),
        ];
    }

    /**
     * Determine max allowed active circles based on trust
     */
    public function getAllowedCircles(User $user): int
    {
        if ($user->trust_score >= 200) return 4;
        if ($user->trust_score >= 100) return 3;
        if ($user->trust_score >= 60) return 2;
        return 1;
    }

    /**
     * Get benefits based on trust score
     */
    public function getBenefits(User $user): array
    {
        $benefits = [];
        $score = $user->trust_score;

        if ($score >= 30) {
            $benefits[] = 'الانضمام لحلقات حتى 2';
        }
        if ($score >= 60) {
            $benefits[] = 'أولوية في اقتراحات التوفيق';
        }
        if ($score >= 100) {
            $benefits[] = 'الانضمام لحلقات حتى 3';
            $benefits[] = 'إمكانية القبض في الجولات الأولى';
        }
        if ($score >= 200) {
            $benefits[] = 'الانضمام لحلقات حتى 4';
            $benefits[] = 'شارة "عضو رائد"';
            $benefits[] = 'رسوم ضمان مخفضة (3% بدل 5%)';
        }

        return $benefits;
    }

    /**
     * Calculate payout priority based on trust score
     */
    public function calculatePayoutPriority(array $members): array
    {
        // Sort by trust score descending — highest trust gets earlier payouts
        usort($members, fn($a, $b) => $b['trust_score'] <=> $a['trust_score']);

        return array_map(function ($member, $index) {
            $member['suggested_payout_order'] = $index + 1;
            return $member;
        }, $members, array_keys($members));
    }
}
