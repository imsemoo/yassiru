<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained('fund_circles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('terms'); // بنود العقد
            $table->decimal('monthly_amount', 12, 2);
            $table->integer('total_months');
            $table->decimal('guarantee_fee_percent', 5, 2)->default(5.00);
            $table->decimal('service_fee_percent', 5, 2)->default(3.00);
            $table->text('penalties'); // بنود العقوبات
            $table->boolean('accepted')->default(false);
            $table->timestamp('accepted_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('device_fingerprint', 255)->nullable();
            $table->string('signature_hash', 255)->nullable(); // hash of user_id + circle_id + timestamp
            $table->timestamps();

            $table->unique(['circle_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_contracts');
    }
};
