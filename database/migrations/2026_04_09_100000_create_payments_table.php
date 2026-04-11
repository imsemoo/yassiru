<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // PaymentType enum
            $table->morphs('payable'); // payable_type + payable_id
            $table->string('provider')->default('fawry');
            $table->string('method')->nullable(); // PaymentMethod enum
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('EGP');
            $table->string('status')->default('pending'); // PaymentStatus enum
            $table->string('merchant_ref')->unique(); // our reference
            $table->string('gateway_ref')->nullable(); // Fawry's reference
            $table->json('gateway_response')->nullable();
            $table->text('checkout_url')->nullable();
            $table->string('fawry_ref_code')->nullable()->index(); // offline Fawry code
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('gateway_ref');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
