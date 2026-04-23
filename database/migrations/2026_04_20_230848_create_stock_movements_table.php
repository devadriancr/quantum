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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('movement_number')->unique();
            $table->date('movement_date');
            $table->time('movement_time');
            $table->foreignId('transaction_type_id')->nullable()->constrained('transaction_types');
            $table->enum('movement_type', ['INBOUND', 'OUTBOUND', 'ADJUSTMENT', 'TRANSFER', 'RETURN']);
            $table->foreignId('location_id_from')->nullable()->constrained('locations');
            $table->foreignId('location_id_to')->nullable()->constrained('locations');
            $table->foreignId('partner_id')->nullable()->constrained('partners');
            $table->foreignId('container_id')->nullable()->constrained('containers');
            $table->foreignId('shipment_document_id')->nullable()->constrained('shipment_documents');
            $table->text('notes')->nullable();
            $table->enum('status', ['PENDING', 'COMPLETED', 'CANCELED', 'RECEIVED', 'VERIFIED', 'RECORDED', 'REJECTED'])->default('PENDING');
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users');
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
        Schema::dropIfExists('stock_movements');
    }
};
