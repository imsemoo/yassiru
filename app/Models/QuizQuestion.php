<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = [
        'track', 'question', 'options', 'correct_option', 'difficulty', 'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_option' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTrack($query, string $track)
    {
        return $query->where('track', $track);
    }
}
