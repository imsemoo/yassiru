<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guarantors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_member_id')->constrained('circle_members')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('phone', 255);
            $table->string('national_id', 255);
            $table->string('relation', 100); // أب، أخ، عم، صديق
            $table->boolean('confirmed')->default(false); // أكد عبر OTP
            $table->string('confirmation_otp', 10)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('circle_member_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guarantors');
    }
};
