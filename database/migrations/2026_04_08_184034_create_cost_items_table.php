<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('category', 50); // dowry, venue, furniture, clothing, gold, housing, other
            $table->string('label', 255);
            $table->decimal('cost_min', 12, 2); // أقل تكلفة
            $table->decimal('cost_avg', 12, 2); // متوسط
            $table->decimal('cost_max', 12, 2); // أعلى تكلفة
            $table->decimal('yassiru_cost', 12, 2)->nullable(); // التكلفة عبر يسّرو
            $table->string('yassiru_note', 500)->nullable(); // ملاحظة التوفير
            $table->boolean('is_required')->default(true); // هل البند أساسي أم اختياري
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['city_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_items');
    }
};
