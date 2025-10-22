<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id('id_maintenance')->comment('Identificador único del mantenimiento');
            $table->foreignId('vehicle_id')->constrained('vehicles')->comment('Vehículo en mantenimiento');
            $table->foreignId('request_id')->nullable()->constrained('maintenance_requests')->nullOnDelete()->comment('Solicitud asociada');
            $table->string('type', 100)->comment('Tipo de mantenimiento realizado');
            $table->date('maintenance_date')->comment('Fecha en la que se realizó el mantenimiento');
            $table->boolean('approved')->default(false)->comment('Indica si fue aprobado');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
