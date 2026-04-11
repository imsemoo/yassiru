<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
        });

        Schema::table('wedding_registrations', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
        });

        Schema::table('guarantee_fund_transactions', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_id');
        });

        Schema::table('wedding_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_id');
        });

        Schema::table('guarantee_fund_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_id');
        });
    }
};
