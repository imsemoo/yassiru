<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeddingRegistration extends Model
{
    protected $fillable = [
        'wedding_id', 'user_id', 'payment_status', 'payment_ref', 'notes',
        'amount_paid', 'payment_id', 'refund_status', 'refund_amount', 'cancelled_at',
    ];

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(GroupWedding::class, 'wedding_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
