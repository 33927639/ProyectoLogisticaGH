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
            // Si existe id_user, eliminarlo completamente
            if (Schema::hasColumn('tbl_incomes', 'id_user')) {
                // Primero eliminar foreign key si existe
                try {
                    $table->dropForeign(['id_user']);
                } catch (\Exception $e) {
                    // Foreign key no existe, continuar
                }
                
                // Eliminar la columna
                $table->dropColumn('id_user');
            }
            
            // Asegurar que id_customer existe y tiene foreign key
            if (!Schema::hasColumn('tbl_incomes', 'id_customer')) {
                $table->bigInteger('id_customer')->unsigned()->after('income_date');
                $table->foreign('id_customer')->references('id_customer')->on('tbl_customers')->onDelete('restrict')->onUpdate('restrict');
            } else {
                // Verificar que tiene foreign key
                try {
                    $table->foreign('id_customer')->references('id_customer')->on('tbl_customers')->onDelete('restrict')->onUpdate('restrict');
                } catch (\Exception $e) {
                    // Foreign key ya existe, continuar
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
            // Recrear id_user si se revierte
            if (!Schema::hasColumn('tbl_incomes', 'id_user')) {
                $table->bigInteger('id_user')->unsigned()->after('income_date');
                $table->foreign('id_user')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            }
        });
    }
};
