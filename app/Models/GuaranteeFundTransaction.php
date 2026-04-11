<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuaranteeFundTransaction extends Model
{
    protected $fillable = [
        'circle_id', 'member_id', 'type', 'amount', 'reason', 'payment_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function circle(): BelongsTo
    {
        return $this->belongsTo(FundCircle::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(CircleMember::class);
    }
}
