<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalContract extends Model
{
    protected $fillable = [
        'circle_id', 'user_id', 'terms', 'monthly_amount', 'total_months',
        'guarantee_fee_percent', 'service_fee_percent', 'penalties',
        'accepted', 'accepted_at', 'ip_address', 'user_agent',
        'device_fingerprint', 'signature_hash',
    ];

    protected $casts = [
        'accepted' => 'boolean',
        'accepted_at' => 'datetime',
        'monthly_amount' => 'decimal:2',
        'guarantee_fee_percent' => 'decimal:2',
        'service_fee_percent' => 'decimal:2',
    ];

    public function circle(): BelongsTo
    {
        return $this->belongsTo(FundCircle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
