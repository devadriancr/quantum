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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->integer('row');
            $table->integer('rack');
            $table->integer('shelf');
            $table->enum('zone', ['RECEIVING', 'STORAGE', 'SHIPPING']);
            $table->double('available_capacity', 15, 2);
            $table->enum('status', ['ACTIVE', 'BLOCKED', 'AVAILABLE', 'OCCUPIED', 'RESERVED', 'MAINTENANCE'])->default('AVAILABLE');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
