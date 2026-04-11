<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupWedding extends Model
{
    protected $fillable = [
        'city_id', 'title', 'venue_name', 'wedding_date', 'max_grooms',
        'registered_count', 'price_per_groom', 'original_price',
        'description', 'status', 'registration_deadline',
    ];

    protected function casts(): array
    {
        return [
            'wedding_date' => 'date',
            'price_per_groom' => 'decimal:2',
            'original_price' => 'decimal:2',
            'registration_deadline' => 'date',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(WeddingRegistration::class, 'wedding_id');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }
}
