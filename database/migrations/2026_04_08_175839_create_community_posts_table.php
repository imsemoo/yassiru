<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('content');
            $table->enum('category', ['advice', 'experience', 'question', 'tip'])->default('advice');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->index('category');
            $table->index('is_approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_posts');
    }
};
