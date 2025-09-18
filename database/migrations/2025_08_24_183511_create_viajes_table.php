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
        Schema::create('viajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camion_id')->constrained('camiones')->onDelete('cascade');
            $table->foreignId('piloto_id')->constrained('pilotos')->onDelete('cascade');
            $table->foreignId('ruta_id')->constrained('rutas')->onDelete('cascade');
            $table->decimal('kilometraje_inicial', 10, 2);
            $table->decimal('kilometraje_final', 10, 2)->nullable();
            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin')->nullable();
            $table->enum('estado', ['Programado', 'En Curso', 'Completado', 'Cancelado'])->default('Programado');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('estado');
            $table->index('fecha_inicio');
            $table->index(['camion_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viajes');
    }
};
