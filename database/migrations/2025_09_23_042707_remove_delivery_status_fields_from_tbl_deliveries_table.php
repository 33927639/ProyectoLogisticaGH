<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminar foreign key específica y luego las columnas
        DB::statement('ALTER TABLE tbl_deliveries DROP FOREIGN KEY FK__tbl_deliv__id_st__6FE99F9F');
        
        Schema::table('tbl_deliveries', function (Blueprint $table) {
            // Eliminar columnas
            if (Schema::hasColumn('tbl_deliveries', 'delivery_status')) {
                $table->dropColumn('delivery_status');
            }
            if (Schema::hasColumn('tbl_deliveries', 'id_status')) {
                $table->dropColumn('id_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_deliveries', function (Blueprint $table) {
            // Restaurar columnas en caso de rollback
            $table->string('delivery_status', 50)->nullable();
            $table->unsignedBigInteger('id_status')->nullable();
            
            // Restaurar foreign key
            $table->foreign('id_status')->references('id_status')->on('tbl_delivery_statuses')->onDelete('restrict')->onUpdate('restrict');
        });
    }
};
