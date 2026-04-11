<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->enum('track', ['shariah', 'psychology', 'financial', 'practical']);
            $table->text('question');
            $table->json('options'); // array of 4 options
            $table->tinyInteger('correct_option'); // 0-3
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['track', 'is_active']);
            $table->index('difficulty');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
