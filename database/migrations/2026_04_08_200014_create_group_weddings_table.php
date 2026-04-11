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
        Schema::create('group_weddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('venue_name', 255);
            $table->date('wedding_date');
            $table->unsignedInteger('max_grooms')->default(20);
            $table->unsignedInteger('registered_count')->default(0);
            $table->decimal('price_per_groom', 12, 2);
            $table->decimal('original_price', 12, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['upcoming', 'full', 'completed', 'cancelled'])->default('upcoming');
            $table->date('registration_deadline');
            $table->timestamps();

            $table->index(['city_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_weddings');
    }
};
