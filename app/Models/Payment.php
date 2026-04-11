<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $fillable = [
        'uuid', 'user_id', 'type', 'payable_type', 'payable_id',
        'provider', 'method', 'amount', 'currency', 'status',
        'merchant_ref', 'gateway_ref', 'gateway_response',
        'checkout_url', 'fawry_ref_code',
        'paid_at', 'expires_at', 'refund_amount', 'metadata',
    ];

    protected $casts = [
        'type' => PaymentType::class,
        'status' => PaymentStatus::class,
        'method' => PaymentMethod::class,
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $hidden = ['gateway_response'];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (!$payment->uuid) {
                $payment->uuid = Str::uuid();
            }
            if (!$payment->merchant_ref) {
                $payment->merchant_ref = 'YSR-' . strtoupper(Str::random(12));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::Pending;
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }

    public function isExpired(): bool
    {
        return $this->status === PaymentStatus::Expired
            || ($this->expires_at && $this->expires_at->isPast() && $this->isPending());
    }
}
