<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kilometers', function (Blueprint $table) {
            $table->id('id_kilometer')->comment('Identificador del registro de kilometraje');
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete()->comment('Entrega relacionada al recorrido');
            $table->foreignId('vehicle_id')->constrained('vehicles')->comment('Vehículo utilizado');
            $table->foreignId('alert_id')->nullable()->constrained('alert_statuses')->nullOnDelete()->comment('Alerta activada, si aplica');
            $table->decimal('kilometers_traveled', 10, 2)->comment('Cantidad de kilómetros recorridos');
            $table->date('record_date')->comment('Fecha del registro de kilometraje');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kilometers');
    }
};
