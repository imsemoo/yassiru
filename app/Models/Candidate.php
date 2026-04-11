<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'recommender_id',
        'name',
        'gender',
        'age',
        'education',
        'occupation',
        'city_id',
        'marital_status',
        'religiosity_level',
        'guardian_name',
        'guardian_phone',
        'guardian_relation',
        'preferences',
        'recommender_notes',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'preferences' => 'array',
        ];
    }

    // ──────────────────────────────────────────────
    // Relations
    // ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recommender(): BelongsTo
    {
        return $this->belongsTo(Recommender::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeMale($query)
    {
        return $query->where('gender', 'male');
    }

    public function scopeFemale($query)
    {
        return $query->where('gender', 'female');
    }
}
