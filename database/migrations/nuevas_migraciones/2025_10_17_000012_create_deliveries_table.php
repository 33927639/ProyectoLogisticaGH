<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id('id_delivery')->comment('Identificador único de la entrega');
            $table->date('delivery_date')->comment('Fecha programada de entrega');
            $table->foreignId('route_id')->constrained('routes')->comment('Ruta asociada a la entrega');
            $table->foreignId('status_id')->constrained('delivery_statuses')->comment('Estado actual de la entrega');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
