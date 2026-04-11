<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeddingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'venue_name' => $this->venue_name,
            'wedding_date' => $this->wedding_date,
            'max_grooms' => $this->max_grooms,
            'registered_count' => $this->registered_count,
            'price_per_groom' => $this->price_per_groom,
            'original_price' => $this->original_price,
            'savings_percent' => $this->original_price > 0
                ? round((1 - $this->price_per_groom / $this->original_price) * 100)
                : 0,
            'description' => $this->description,
            'status' => $this->status,
            'registration_deadline' => $this->registration_deadline,
            'city' => $this->whenLoaded('city', fn() => [
                'id' => $this->city->id,
                'name' => $this->city->name,
            ]),
            'registrations' => $this->whenLoaded('registrations'),
        ];
    }
}
