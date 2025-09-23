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
        Schema::table('tbl_incomes', function (Blueprint $table) {
            // Solo ejecutar si existe id_user y no existe id_customer
            if (Schema::hasColumn('tbl_incomes', 'id_user') && !Schema::hasColumn('tbl_incomes', 'id_customer')) {
                // Renombrar la columna sin eliminar foreign key primero
                $table->renameColumn('id_user', 'id_customer');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_incomes', function (Blueprint $table) {
            // Eliminar foreign key
            $table->dropForeign(['id_customer']);
            
            // Renombrar de vuelta
            $table->renameColumn('id_customer', 'id_user');
            
            // Restaurar foreign key original
            $table->foreign('id_user')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
        });
    }
};
