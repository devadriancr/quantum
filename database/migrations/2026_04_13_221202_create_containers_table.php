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
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignId('partner_id')->constrained('partners');
            $table->enum('container_type', ['TRUCK', 'CONTAINER', 'BOX', 'PALLET', 'OTHER']);
            $table->date('estimated_arrival_date')->nullable();
            $table->time('estimated_arrival_time')->nullable();
            $table->date('actual_arrival_date')->nullable();
            $table->time('actual_arrival_time')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['PENDING', 'EXPECTED', 'ARRIVED', 'UNLOADING', 'INSPECTION', 'RECEIVED', 'REJECTED', 'IN_TRANSIT'])->default('PENDING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
