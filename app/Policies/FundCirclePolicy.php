<?php

namespace App\Policies;

use App\Models\FundCircle;
use App\Models\User;

class FundCirclePolicy
{
    public function view(User $user, FundCircle $circle): bool
    {
        return $circle->members()->where('user_id', $user->id)->exists()
            || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->has_certificate;
    }

    public function join(User $user, FundCircle $circle): bool
    {
        return $circle->status === 'forming'
            && !$circle->members()->where('user_id', $user->id)->exists();
    }

    public function contribute(User $user, FundCircle $circle): bool
    {
        return $circle->status === 'active'
            && $circle->members()->where('user_id', $user->id)->exists();
    }
}
