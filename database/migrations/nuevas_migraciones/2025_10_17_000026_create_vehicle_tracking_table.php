<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_tracking', function (Blueprint $table) {
            $table->id('id_tracking')->comment('Identificador único del registro de ubicación del vehículo');
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete()->comment('Vehículo rastreado');
            $table->decimal('latitude', 10, 7)->comment('Latitud de la ubicación');
            $table->decimal('longitude', 10, 7)->comment('Longitud de la ubicación');
            $table->decimal('speed_kmh', 6, 2)->nullable()->comment('Velocidad del vehículo en km/h');
            $table->dateTime('recorded_at')->useCurrent()->comment('Fecha y hora del registro de posición');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_tracking');
    }
};
