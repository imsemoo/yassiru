<?php

namespace App\Services;

use App\Models\CounselingSession;

class CounselingService
{
    private const AVAILABLE_SLOTS = ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'];

    public function hasConflict(int $userId, string $date): bool
    {
        return CounselingSession::where('user_id', $userId)
            ->where('status', 'scheduled')
            ->whereDate('scheduled_at', $date)
            ->exists();
    }

    public function getAvailableSlots(string $date): array
    {
        $bookedSlots = CounselingSession::whereDate('scheduled_at', $date)
            ->where('status', 'scheduled')
            ->pluck('scheduled_at')
            ->map(fn($dt) => $dt->format('H:i'))
            ->toArray();

        return array_map(fn($slot) => [
            'time' => $slot,
            'available' => !in_array($slot, $bookedSlots),
        ], self::AVAILABLE_SLOTS);
    }
}
