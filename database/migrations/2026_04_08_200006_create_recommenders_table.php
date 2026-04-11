<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recommenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['imam', 'teacher', 'relative', 'community_leader']);
            $table->string('institution', 255)->nullable();
            $table->text('bio')->nullable();
            $table->unsignedInteger('candidates_count')->default(0);
            $table->unsignedInteger('successful_matches')->default(0);
            $table->boolean('is_approved')->default(false);
            $table->boolean('honor_pledge_signed')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommenders');
    }
};
