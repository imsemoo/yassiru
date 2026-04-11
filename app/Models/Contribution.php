<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contribution extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'circle_id',
        'member_id',
        'amount',
        'round_number',
        'payment_ref',
        'status',
        'due_date',
        'paid_at',
        'payment_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    // ──────────────────────────────────────────────
    // Relations
    // ──────────────────────────────────────────────

    public function circle(): BelongsTo
    {
        return $this->belongsTo(FundCircle::class, 'circle_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(CircleMember::class, 'member_id');
    }
}
