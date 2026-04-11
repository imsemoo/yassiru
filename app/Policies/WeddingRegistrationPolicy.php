<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WeddingRegistration;

class WeddingRegistrationPolicy
{
    public function cancel(User $user, WeddingRegistration $registration): bool
    {
        return $registration->user_id === $user->id
            && $registration->payment_status !== 'paid';
    }

    public function pay(User $user, WeddingRegistration $registration): bool
    {
        return $registration->user_id === $user->id
            && $registration->payment_status === 'pending';
    }
}
