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
        Schema::create('reception_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_movement_id')->constrained('stock_movements')->onDelete('cascade');
            $table->foreignId('shipment_document_id')->nullable()->constrained('shipment_documents');
            $table->text('scan_content');
            $table->enum('consignment_type', ['MY', 'MC', 'MH', 'MZ', 'UNKNOWN'])->default('UNKNOWN');
            $table->string('parsed_serial')->nullable();
            $table->string('parsed_item_code')->nullable();
            $table->string('parsed_supplier')->nullable();
            $table->decimal('parsed_quantity', 10, 2)->nullable();
            $table->enum('scan_status', ['MATCHED', 'UNMATCHED', 'DUPLICATE', 'ERROR'])->default('UNMATCHED');
            $table->text('error_message')->nullable();
            $table->foreignId('matched_document_line_id')->nullable()->constrained('shipment_document_lines');
            $table->foreignId('stock_movement_line_id')->nullable()->constrained('stock_movement_lines');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reception_scans');
    }
};
