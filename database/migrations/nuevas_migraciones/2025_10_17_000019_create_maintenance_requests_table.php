<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id('id_request')->comment('Identificador único de la solicitud de mantenimiento');
            $table->foreignId('vehicle_id')->constrained('vehicles')->comment('Vehículo que solicita mantenimiento');
            $table->date('request_date')->comment('Fecha en que se solicitó el mantenimiento');
            $table->text('reason')->comment('Motivo o descripción de la solicitud');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->comment('Usuario que aprobó la solicitud');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
