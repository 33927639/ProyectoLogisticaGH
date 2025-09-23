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
            // Verificar si existe id_user y necesita ser renombrado
            if (Schema::hasColumn('tbl_incomes', 'id_user') && !Schema::hasColumn('tbl_incomes', 'id_customer')) {
                // Eliminar la foreign key de id_user si existe
                try {
                    $table->dropForeign(['id_user']);
                } catch (\Exception $e) {
                    // Foreign key no existe, continuar
                }
                
                // Renombrar id_user a id_customer
                $table->renameColumn('id_user', 'id_customer');
                
                // Crear la nueva foreign key
                $table->foreign('id_customer')->references('id_customer')->on('tbl_customers')->onDelete('restrict')->onUpdate('restrict');
            }
            
            // Si ya existe id_customer pero no tiene foreign key, añadirla
            if (Schema::hasColumn('tbl_incomes', 'id_customer')) {
                try {
                    $table->foreign('id_customer')->references('id_customer')->on('tbl_customers')->onDelete('restrict')->onUpdate('restrict');
                } catch (\Exception $e) {
                    // La foreign key ya existe, continuar
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_incomes', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_incomes', 'id_customer')) {
                // Eliminar foreign key
                try {
                    $table->dropForeign(['id_customer']);
                } catch (\Exception $e) {
                    // Foreign key no existe
                }
                
                // Renombrar de vuelta a id_user
                $table->renameColumn('id_customer', 'id_user');
                
                // Recrear foreign key con users
                $table->foreign('id_user')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            }
        });
    }
};
