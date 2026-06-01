<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('location_id')->constrained('locations');
            $table->unique(['item_id', 'location_id']);
            $table->decimal('daily_average', 15, 4)->default(0);
            $table->decimal('minimum_quantity', 15, 2)->default(0);
            $table->decimal('maximum_quantity', 15, 2)->default(0);
            $table->decimal('reorder_point', 15, 2)->default(0);
            $table->decimal('reorder_quantity', 15, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users');
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_limits');
    }
};
