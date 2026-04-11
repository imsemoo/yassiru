<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FundCircleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'max_members' => $this->max_members,
            'monthly_amount' => $this->monthly_amount,
            'currency' => $this->currency,
            'cycle_months' => $this->cycle_months,
            'current_round' => $this->current_round,
            'status' => $this->status,
            'payout_method' => $this->payout_method,
            'started_at' => $this->started_at?->format('Y-m-d'),
            'city' => $this->whenLoaded('city', fn() => [
                'id' => $this->city->id,
                'name' => $this->city->name,
            ]),
            'members' => $this->whenLoaded('members'),
            'total_payout' => $this->monthly_amount * $this->max_members,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
