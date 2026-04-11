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
        Schema::create('fund_circles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->unsignedInteger('max_members')->default(15);
            $table->decimal('monthly_amount', 12, 2);
            $table->string('currency', 10)->default('EGP');
            $table->unsignedInteger('cycle_months');
            $table->unsignedInteger('current_round')->default(0);
            $table->enum('status', ['forming', 'active', 'completed', 'cancelled'])->default('forming');
            $table->enum('payout_method', ['lottery', 'priority', 'schedule'])->default('schedule');
            $table->timestamp('started_at')->nullable();
            $table->timestamps();

            $table->index(['city_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_circles');
    }
};
