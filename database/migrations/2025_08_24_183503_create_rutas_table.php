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
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();
            $table->string('origen');
            $table->string('destino');
            $table->decimal('distancia_km', 8, 2);
            $table->decimal('tiempo_estimado_horas', 5, 2);
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['Activa', 'Inactiva'])->default('Activa');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['origen', 'destino']);
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
