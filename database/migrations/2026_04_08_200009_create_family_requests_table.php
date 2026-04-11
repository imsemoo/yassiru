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
        Schema::create('family_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recommendation_id')->constrained('recommendations')->cascadeOnDelete();
            $table->enum('initiated_by', ['male_family', 'female_family']);
            $table->enum('status', ['pending', 'accepted', 'rejected', 'meeting_scheduled'])->default('pending');
            $table->dateTime('meeting_date')->nullable();
            $table->string('meeting_location', 500)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_requests');
    }
};
