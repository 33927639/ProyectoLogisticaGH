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
            // Primero eliminar la clave primaria compuesta
            $table->dropPrimary(['delivery_id', 'vehicle_id', 'driver_id']);
        });
        
        Schema::table('delivery_assignments', function (Blueprint $table) {
            // Agregar columna ID auto-incremental como nueva primary key
            $table->id()->first();
            
            // Crear índice único para mantener la integridad
            $table->unique(['delivery_id', 'vehicle_id', 'driver_id'], 'delivery_assignment_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_assignments', function (Blueprint $table) {
            // Eliminar índice único e ID
            $table->dropUnique('delivery_assignment_unique');
            $table->dropColumn('id');
        });
        
        Schema::table('delivery_assignments', function (Blueprint $table) {
            // Restaurar clave primaria compuesta
            $table->primary(['delivery_id', 'vehicle_id', 'driver_id']);
        });
    }
};
