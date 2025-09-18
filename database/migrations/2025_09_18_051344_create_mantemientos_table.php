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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('camion_id');
            $table->string('tipo_mantenimiento')->index();
            $table->text('descripcion')->nullable();
            $table->date('fecha_programada')->index();
            $table->date('fecha_realizada')->nullable();
            $table->decimal('costo', 10)->nullable();
            $table->enum('estado', ['Programado', 'En Proceso', 'Completado', 'Cancelado'])->default('Programado')->index();
            $table->timestamps();

            $table->index(['camion_id', 'estado']);
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
