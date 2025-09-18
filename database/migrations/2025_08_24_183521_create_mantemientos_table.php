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
        Schema::create('mantemientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camion_id')->constrained('camiones')->onDelete('cascade');
            $table->string('tipo_mantenimiento');
            $table->text('descripcion')->nullable();
            $table->date('fecha_programada');
            $table->date('fecha_realizada')->nullable();
            $table->decimal('costo', 10, 2)->nullable();
            $table->enum('estado', ['Programado', 'En Proceso', 'Completado', 'Cancelado'])->default('Programado');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('estado');
            $table->index('fecha_programada');
            $table->index(['camion_id', 'estado']);
            $table->index('tipo_mantenimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantemientos');
    }
};
