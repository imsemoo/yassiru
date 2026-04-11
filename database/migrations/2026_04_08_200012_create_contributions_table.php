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
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained('fund_circles');
            $table->foreignId('member_id')->constrained('circle_members');
            $table->decimal('amount', 12, 2);
            $table->unsignedInteger('round_number');
            $table->string('payment_ref', 255)->nullable();
            $table->enum('status', ['pending', 'paid', 'late', 'failed'])->default('pending');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['circle_id', 'round_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};
