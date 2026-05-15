<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->foreignId('item_class_id')->nullable()->constrained('item_classes');
            $table->foreignId('item_type_id')->nullable()->constrained('item_types');
            $table->foreignId('measurement_unit_id')->nullable()->constrained('measurement_units');
            $table->foreignId('packing_specification_id')->nullable()->constrained('packing_specifications');
            $table->integer('default_safety_stock')->nullable();
            $table->decimal('last_unit_cost', 15, 4)->nullable();
            $table->string('last_currency_code', 10)->nullable();
            $table->boolean('active')->default(true);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users');
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
