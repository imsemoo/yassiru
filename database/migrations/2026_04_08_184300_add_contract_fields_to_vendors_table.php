<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->boolean('has_contract')->default(false)->after('is_active');
            $table->date('contract_start')->nullable()->after('has_contract');
            $table->date('contract_end')->nullable()->after('contract_start');
            $table->decimal('deposit_paid', 12, 2)->default(0)->after('contract_end');
            $table->string('backup_vendor_id')->nullable()->after('deposit_paid'); // بديل جاهز
            $table->decimal('rating', 3, 2)->default(0)->after('backup_vendor_id');
            $table->integer('rating_count')->default(0)->after('rating');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'has_contract', 'contract_start', 'contract_end',
                'deposit_paid', 'backup_vendor_id', 'rating', 'rating_count',
            ]);
        });
    }
};
