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
            $table->bigIncrements('id');
            $table->string('origen');
            $table->string('destino');
            $table->decimal('distancia_km');
            $table->decimal('tiempo_estimado_horas', 5);
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['Activa', 'Inactiva'])->default('Activa')->index();
            $table->timestamps();

            $table->index(['origen', 'destino']);
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
