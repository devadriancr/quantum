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
            $table->string('description');
            $table->foreignId('item_class_id')->constrained('item_classes');
            $table->foreignId('item_type_id')->constrained('item_types');
            $table->foreignId('measurement_unit_id')->constrained('measurement_units');
            $table->foreignId('packing_specification_id')->constrained('packing_specifications');
            $table->integer('default_safety_stock');
            $table->decimal('last_unit_cost', 15, 2);
            $table->boolean('active')->default(true);
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
