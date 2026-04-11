<?php

namespace App\Policies;

use App\Models\Recommender;
use App\Models\User;

class RecommenderPolicy
{
    public function addCandidate(User $user, Recommender $recommender): bool
    {
        return $recommender->user_id === $user->id && $recommender->is_approved;
    }

    public function viewDashboard(User $user, Recommender $recommender): bool
    {
        return $recommender->user_id === $user->id;
    }
}
