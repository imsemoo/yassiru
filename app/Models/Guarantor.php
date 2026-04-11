<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guarantor extends Model
{
    protected $fillable = [
        'circle_member_id', 'name', 'phone', 'national_id', 'relation',
        'confirmed', 'confirmation_otp', 'otp_expires_at', 'confirmed_at', 'ip_address',
    ];

    protected $casts = [
        'confirmed' => 'boolean',
        'otp_expires_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    protected $hidden = ['confirmation_otp', 'national_id'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(CircleMember::class, 'circle_member_id');
    }
}
