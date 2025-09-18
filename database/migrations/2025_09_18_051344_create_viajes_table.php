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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('camion_id');
            $table->unsignedBigInteger('piloto_id')->index('viajes_piloto_id_foreign');
            $table->unsignedBigInteger('ruta_id')->index('viajes_ruta_id_foreign');
            $table->decimal('kilometraje_inicial', 10);
            $table->decimal('kilometraje_final', 10)->nullable();
            $table->timestamp('fecha_inicio')->index();
            $table->timestamp('fecha_fin')->nullable();
            $table->enum('estado', ['Programado', 'En Curso', 'Completado', 'Cancelado'])->default('Programado')->index();
            $table->timestamps();

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
