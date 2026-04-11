<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'gender' => $this->gender,
            'is_verified' => $this->is_verified,
            'has_certificate' => $this->has_certificate,
            'city' => $this->whenLoaded('city', fn() => [
                'id' => $this->city->id,
                'name' => $this->city->name,
            ]),
            'date_of_birth' => $this->date_of_birth,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
