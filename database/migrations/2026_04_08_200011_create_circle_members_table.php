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
        Schema::create('circle_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained('fund_circles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->unsignedInteger('payout_order')->nullable();
            $table->boolean('has_received_payout')->default(false);
            $table->decimal('total_contributed', 12, 2)->default(0);
            $table->enum('status', ['active', 'frozen', 'removed'])->default('active');
            $table->timestamp('joined_at');
            $table->timestamps();

            $table->unique(['circle_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circle_members');
    }
};
