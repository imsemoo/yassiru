<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecommenderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'institution' => $this->institution,
            'bio' => $this->bio,
            'candidates_count' => $this->candidates_count,
            'successful_matches' => $this->successful_matches,
            'is_approved' => $this->is_approved,
            'honor_pledge_signed' => $this->honor_pledge_signed,
            'approved_at' => $this->approved_at?->format('Y-m-d'),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
