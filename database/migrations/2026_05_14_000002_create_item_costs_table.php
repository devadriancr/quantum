<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->string('vendor_number', 20)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('total_cost', 15, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_costs');
    }
};