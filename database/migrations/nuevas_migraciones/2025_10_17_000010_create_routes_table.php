<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id('id_route')->comment('Identificador de la ruta');
            $table->foreignId('origin_id')->constrained('municipalities')->comment('Municipio de origen');
            $table->foreignId('destination_id')->constrained('municipalities')->comment('Municipio de destino');
            $table->decimal('distance_km', 10, 2)->comment('Distancia total en kilómetros');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
