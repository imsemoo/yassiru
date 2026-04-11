<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'track',
        'description',
        'duration_hours',
        'lessons_count',
        'order_index',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duration_hours' => 'decimal:1',
            'is_active' => 'boolean',
        ];
    }

    // ──────────────────────────────────────────────
    // Relations
    // ──────────────────────────────────────────────

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
