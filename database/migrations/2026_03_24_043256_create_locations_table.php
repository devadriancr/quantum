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
            $table->integer('row')->nullable();
            $table->integer('rack')->nullable();
            $table->integer('shelf')->nullable();
            $table->enum('zone', ['RECEIVING', 'STORAGE', 'SHIPPING'])->nullable();
            $table->double('available_capacity', 15, 2)->nullable();
            $table->enum('status', ['ACTIVE', 'BLOCKED', 'AVAILABLE', 'OCCUPIED', 'RESERVED', 'MAINTENANCE', 'UNAVAILABLE'])->default('AVAILABLE');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses');
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
        Schema::dropIfExists('locations');
    }
};
