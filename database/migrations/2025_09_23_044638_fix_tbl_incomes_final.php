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
        // Ejecutar comandos SQL individuales
        try {
            DB::statement("SET foreign_key_checks = 0");
            
            // Intentar eliminar foreign keys existentes
            try {
                DB::statement("ALTER TABLE tbl_incomes DROP FOREIGN KEY FK__tbl_incom__id_us__1DB06A4F");
            } catch (\Exception $e) {
                // Foreign key no existe, continuar
            }
            
            try {
                DB::statement("ALTER TABLE tbl_incomes DROP FOREIGN KEY tbl_incomes_id_user_foreign");
            } catch (\Exception $e) {
                // Foreign key no existe, continuar
            }
            
            try {
                DB::statement("ALTER TABLE tbl_incomes DROP FOREIGN KEY fk_tbl_incomes_id_user");
            } catch (\Exception $e) {
                // Foreign key no existe, continuar
            }
            
            // Eliminar columna id_user si existe
            $hasIdUser = DB::select("SELECT COUNT(*) as count FROM information_schema.columns WHERE table_name = 'tbl_incomes' AND column_name = 'id_user' AND table_schema = DATABASE()");
            if ($hasIdUser[0]->count > 0) {
                DB::statement("ALTER TABLE tbl_incomes DROP COLUMN id_user");
            }
            
            // Agregar columna id_customer si no existe
            $hasIdCustomer = DB::select("SELECT COUNT(*) as count FROM information_schema.columns WHERE table_name = 'tbl_incomes' AND column_name = 'id_customer' AND table_schema = DATABASE()");
            if ($hasIdCustomer[0]->count == 0) {
                DB::statement("ALTER TABLE tbl_incomes ADD COLUMN id_customer BIGINT UNSIGNED NOT NULL AFTER income_date");
            }
            
            // Agregar foreign key para id_customer
            try {
                DB::statement("ALTER TABLE tbl_incomes ADD CONSTRAINT fk_tbl_incomes_id_customer FOREIGN KEY (id_customer) REFERENCES tbl_customers(id_customer) ON DELETE RESTRICT ON UPDATE RESTRICT");
            } catch (\Exception $e) {
                // Foreign key ya existe, continuar
            }
            
            DB::statement("SET foreign_key_checks = 1");
            
        } catch (\Exception $e) {
            DB::statement("SET foreign_key_checks = 1");
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("SET foreign_key_checks = 0");
            
            // Eliminar foreign key de id_customer
            try {
                DB::statement("ALTER TABLE tbl_incomes DROP FOREIGN KEY fk_tbl_incomes_id_customer");
            } catch (\Exception $e) {
                // Foreign key no existe
            }
            
            // Eliminar columna id_customer
            DB::statement("ALTER TABLE tbl_incomes DROP COLUMN id_customer");
            
            // Recrear columna id_user
            DB::statement("ALTER TABLE tbl_incomes ADD COLUMN id_user BIGINT UNSIGNED NOT NULL AFTER income_date");
            
            // Agregar foreign key para id_user
            DB::statement("ALTER TABLE tbl_incomes ADD CONSTRAINT fk_tbl_incomes_id_user FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE RESTRICT");
            
            DB::statement("SET foreign_key_checks = 1");
            
        } catch (\Exception $e) {
            DB::statement("SET foreign_key_checks = 1");
            throw $e;
        }
    }
};
