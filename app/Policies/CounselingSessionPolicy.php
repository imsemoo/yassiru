<?php

namespace App\Policies;

use App\Models\CounselingSession;
use App\Models\User;

class CounselingSessionPolicy
{
    public function cancel(User $user, CounselingSession $session): bool
    {
        return $session->user_id === $user->id
            && $session->status === 'scheduled'
            && $session->scheduled_at->diffInHours(now()) >= 24;
    }

    public function view(User $user, CounselingSession $session): bool
    {
        return $session->user_id === $user->id || $user->hasRole('admin');
    }
}
