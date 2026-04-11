<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'gender' => $this->gender,
            'age' => $this->age,
            'education' => $this->education,
            'occupation' => $this->occupation,
            'marital_status' => $this->marital_status,
            'religiosity_level' => $this->religiosity_level,
            'status' => $this->status,
            'city' => $this->whenLoaded('city', fn() => [
                'id' => $this->city->id,
                'name' => $this->city->name,
            ]),
            'guardian_name' => $this->guardian_name,
            'guardian_relation' => $this->guardian_relation,
            'recommender_notes' => $this->recommender_notes,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
