<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CircleMember extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'circle_id',
        'user_id',
        'payout_order',
        'has_received_payout',
        'total_contributed',
        'status',
        'joined_at',
        'guarantee_deposit',
        'has_guarantor',
        'contract_signed',
        'late_count',
        'is_banned',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_received_payout' => 'boolean',
            'total_contributed' => 'decimal:2',
            'joined_at' => 'datetime',
        ];
    }

    // ──────────────────────────────────────────────
    // Relations
    // ──────────────────────────────────────────────

    public function circle(): BelongsTo
    {
        return $this->belongsTo(FundCircle::class, 'circle_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class, 'member_id');
    }

    public function payout(): HasOne
    {
        return $this->hasOne(Payout::class, 'member_id');
    }
}
