<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_assignments', function (Blueprint $table) {
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete()->comment('Entrega asignada');
            $table->foreignId('vehicle_id')->constrained('vehicles')->comment('Vehículo asignado');
            $table->foreignId('driver_id')->constrained('drivers')->comment('Conductor asignado');
            $table->date('assignment_date')->comment('Fecha de asignación');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();

            $table->primary(['delivery_id', 'vehicle_id', 'driver_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_assignments');
    }
};
