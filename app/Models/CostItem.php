<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostItem extends Model
{
    protected $fillable = [
        'city_id', 'category', 'label', 'cost_min', 'cost_avg', 'cost_max',
        'yassiru_cost', 'yassiru_note', 'is_required', 'sort_order',
    ];

    protected $casts = [
        'cost_min' => 'decimal:2',
        'cost_avg' => 'decimal:2',
        'cost_max' => 'decimal:2',
        'yassiru_cost' => 'decimal:2',
        'is_required' => 'boolean',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
