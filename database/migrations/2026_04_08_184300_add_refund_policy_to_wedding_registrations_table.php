<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wedding_registrations', function (Blueprint $table) {
            $table->decimal('amount_paid', 12, 2)->default(0)->after('payment_ref');
            $table->decimal('refund_amount', 12, 2)->default(0)->after('amount_paid');
            $table->enum('refund_status', ['none', 'requested', 'approved', 'processed', 'rejected'])->default('none')->after('refund_amount');
            $table->text('refund_reason')->nullable()->after('refund_status');
            $table->timestamp('cancelled_at')->nullable()->after('refund_reason');
        });

        Schema::table('group_weddings', function (Blueprint $table) {
            $table->integer('refund_100_days')->default(30)->after('registration_deadline'); // 100% refund
            $table->integer('refund_50_days')->default(15)->after('refund_100_days'); // 50% refund
            // After refund_50_days: 0% refund
        });
    }

    public function down(): void
    {
        Schema::table('wedding_registrations', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'refund_amount', 'refund_status', 'refund_reason', 'cancelled_at']);
        });
        Schema::table('group_weddings', function (Blueprint $table) {
            $table->dropColumn(['refund_100_days', 'refund_50_days']);
        });
    }
};
