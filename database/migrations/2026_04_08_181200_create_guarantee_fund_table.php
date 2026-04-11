<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guarantee_fund_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained('fund_circles')->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('circle_members')->nullOnDelete();
            $table->enum('type', ['deposit', 'coverage', 'refund']); // إيداع ضمان، تغطية تخلف، استرداد
            $table->decimal('amount', 12, 2);
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['circle_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guarantee_fund_transactions');
    }
};
