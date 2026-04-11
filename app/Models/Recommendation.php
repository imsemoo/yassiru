<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recommendation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'recommender_id',
        'male_candidate_id',
        'female_candidate_id',
        'reason',
        'compatibility_score',
        'status',
        'responded_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'compatibility_score' => 'decimal:2',
            'responded_at' => 'datetime',
        ];
    }

    // ──────────────────────────────────────────────
    // Relations
    // ──────────────────────────────────────────────

    public function recommender(): BelongsTo
    {
        return $this->belongsTo(Recommender::class);
    }

    public function maleCandidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'male_candidate_id');
    }

    public function femaleCandidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'female_candidate_id');
    }

    public function familyRequests(): HasMany
    {
        return $this->hasMany(FamilyRequest::class);
    }
}
