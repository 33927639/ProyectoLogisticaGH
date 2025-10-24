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
        Schema::table('delivery_assignments', function (Blueprint $table) {
            // Eliminar la clave primaria compuesta existente
            $table->dropPrimary(['delivery_id', 'vehicle_id', 'driver_id']);
        });
        
        Schema::table('delivery_assignments', function (Blueprint $table) {
            // Agregar columna ID auto-incrementable al inicio
            $table->id()->first();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->dropColumn('id');
        });
        
        Schema::table('delivery_assignments', function (Blueprint $table) {
            // Restaurar la clave primaria compuesta
            $table->primary(['delivery_id', 'vehicle_id', 'driver_id']);
        });
    }
};
