<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('active_circles_count')->default(0)->after('has_certificate');
            $table->integer('max_active_circles')->default(2)->after('active_circles_count');
            $table->integer('trust_score')->default(0)->after('max_active_circles');
            $table->boolean('is_banned_from_circles')->default(false)->after('trust_score');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['active_circles_count', 'max_active_circles', 'trust_score', 'is_banned_from_circles']);
        });
    }
};
