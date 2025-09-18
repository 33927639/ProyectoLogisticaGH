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
        Schema::create('camiones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('placa')->unique();
            $table->string('marca');
            $table->string('modelo');
            $table->year('year');
            $table->string('numero_motor')->nullable();
            $table->decimal('kilometraje_actual', 10)->default(0);
            $table->integer('intervalo_mantenimiento_km')->default(5000);
            $table->enum('estado', ['Activo', 'En Taller', 'Inactivo'])->default('Activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camiones');
    }
};
