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
        Schema::create('shipment_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('container_id')->nullable()->constrained('containers');
            $table->foreignId('partner_id')->nullable()->constrained('partners');
            $table->string('document_number')->nullable();
            $table->date('document_date')->nullable();
            $table->time('document_time')->nullable();
            $table->date('estimated_arrival_date')->nullable();
            $table->time('estimated_arrival_time')->nullable();
            $table->enum('document_status', ['PENDING', 'RECEIVED', 'PROCESSED', 'PARTIAL', 'COMPLETE', 'DISCREPANCY'])->default('PENDING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_documents');
    }
};
