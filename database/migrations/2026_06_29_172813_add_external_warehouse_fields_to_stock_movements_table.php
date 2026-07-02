<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->string('invoice_number', 100)->nullable()->after('shipment_document_id');
            $table->string('carta_porte', 100)->nullable()->after('invoice_number');
        });

        // PostgreSQL: drop the existing check constraint and recreate with IN_TRANSIT added
        DB::statement('ALTER TABLE stock_movements DROP CONSTRAINT IF EXISTS stock_movements_status_check');
        DB::statement("ALTER TABLE stock_movements ADD CONSTRAINT stock_movements_status_check
            CHECK (status::text = ANY (ARRAY[
                'PENDING','COMPLETED','CANCELED','RECEIVED','VERIFIED','RECORDED','REJECTED','IN_TRANSIT'
            ]::text[]))");
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'carta_porte']);
        });

        DB::statement('ALTER TABLE stock_movements DROP CONSTRAINT IF EXISTS stock_movements_status_check');
        DB::statement("ALTER TABLE stock_movements ADD CONSTRAINT stock_movements_status_check
            CHECK (status::text = ANY (ARRAY[
                'PENDING','COMPLETED','CANCELED','RECEIVED','VERIFIED','RECORDED','REJECTED'
            ]::text[]))");
    }
};
