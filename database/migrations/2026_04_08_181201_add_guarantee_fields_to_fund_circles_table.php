<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fund_circles', function (Blueprint $table) {
            $table->decimal('guarantee_fee_percent', 5, 2)->default(5.00)->after('payout_method');
            $table->decimal('service_fee_percent', 5, 2)->default(3.00)->after('guarantee_fee_percent');
            $table->decimal('guarantee_balance', 12, 2)->default(0)->after('service_fee_percent');
            $table->boolean('requires_guarantor')->default(true)->after('guarantee_balance');
            $table->integer('late_grace_days')->default(3)->after('requires_guarantor');
            $table->integer('freeze_after_days')->default(7)->after('late_grace_days');
            $table->integer('remove_after_days')->default(15)->after('freeze_after_days');
        });

        Schema::table('circle_members', function (Blueprint $table) {
            $table->decimal('guarantee_deposit', 12, 2)->default(0)->after('total_contributed');
            $table->boolean('has_guarantor')->default(false)->after('guarantee_deposit');
            $table->boolean('contract_signed')->default(false)->after('has_guarantor');
            $table->integer('late_count')->default(0)->after('contract_signed');
            $table->boolean('is_banned')->default(false)->after('late_count');
            $table->timestamp('last_reminder_at')->nullable()->after('is_banned');
        });
    }

    public function down(): void
    {
        Schema::table('fund_circles', function (Blueprint $table) {
            $table->dropColumn([
                'guarantee_fee_percent', 'service_fee_percent', 'guarantee_balance',
                'requires_guarantor', 'late_grace_days', 'freeze_after_days', 'remove_after_days',
            ]);
        });

        Schema::table('circle_members', function (Blueprint $table) {
            $table->dropColumn([
                'guarantee_deposit', 'has_guarantor', 'contract_signed',
                'late_count', 'is_banned', 'last_reminder_at',
            ]);
        });
    }
};
