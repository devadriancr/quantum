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
        Schema::create('shipment_document_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_document_id')->constrained('shipment_documents');
            $table->integer('line_number');
            $table->foreignId('item_id')->constrained('items');
            $table->string('serial_number')->nullable();
            $table->decimal('quantity_declared', 10, 2)->default(0);
            $table->enum('status', ['PENDING', 'RECEIVED', 'DAMAGED', 'EXPECTED', 'DISCREPANCY'])->default('PENDING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_document_lines');
    }
};
