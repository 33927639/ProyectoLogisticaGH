<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id('id_vehicle')->comment('Identificador único del vehículo');
            $table->string('license_plate', 20)->unique()->comment('Placa del vehículo');
            $table->integer('capacity')->comment('Capacidad de carga del vehículo');
            $table->boolean('available')->default(true)->comment('Disponible para asignación');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
