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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recommender_id')->constrained('recommenders')->cascadeOnDelete();
            $table->string('name', 255);
            $table->enum('gender', ['male', 'female']);
            $table->unsignedInteger('age');
            $table->string('education', 255)->nullable();
            $table->string('occupation', 255)->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->enum('marital_status', ['single', 'divorced', 'widowed'])->default('single');
            $table->enum('religiosity_level', ['committed', 'moderate', 'learning'])->default('committed');
            $table->string('guardian_name', 255);
            $table->string('guardian_phone', 255);
            $table->string('guardian_relation', 100);
            $table->json('preferences')->nullable();
            $table->text('recommender_notes')->nullable();
            $table->enum('status', ['active', 'matched', 'withdrawn'])->default('active');
            $table->timestamps();

            $table->index('recommender_id');
            $table->index(['gender', 'status']);
            $table->index('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
