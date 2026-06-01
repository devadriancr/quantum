<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->date('week_start_date')->nullable();
            $table->decimal('total_cost', 14, 4)->default(0);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users');
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('unit_plan_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_plan_id')->constrained('unit_plans')->cascadeOnDelete();
            $table->string('container_code');
            $table->date('customs_date')->nullable();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->string('item_code');
            $table->decimal('quantity', 14, 2)->default(0);
            $table->unsignedTinyInteger('day_of_week');
            $table->unsignedTinyInteger('slot_index');
            $table->time('schedule_time')->nullable();
            $table->decimal('unit_cost', 14, 4)->default(0);
            $table->decimal('line_total', 14, 4)->default(0);
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->timestamps();

            $table->index(['unit_plan_id', 'day_of_week', 'slot_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_plan_lines');
        Schema::dropIfExists('unit_plans');
    }
};
