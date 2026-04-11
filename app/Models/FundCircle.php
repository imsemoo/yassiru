<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FundCircle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'city_id',
        'created_by',
        'max_members',
        'monthly_amount',
        'currency',
        'cycle_months',
        'current_round',
        'status',
        'payout_method',
        'started_at',
        'guarantee_fee_percent',
        'service_fee_percent',
        'guarantee_balance',
        'requires_guarantor',
        'late_grace_days',
        'freeze_after_days',
        'remove_after_days',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'monthly_amount' => 'decimal:2',
            'started_at' => 'datetime',
        ];
    }

    // ──────────────────────────────────────────────
    // Relations
    // ──────────────────────────────────────────────

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CircleMember::class, 'circle_id');
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class, 'circle_id');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class, 'circle_id');
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForming($query)
    {
        return $query->where('status', 'forming');
    }
}
