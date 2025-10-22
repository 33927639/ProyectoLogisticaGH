<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_statuses', function (Blueprint $table) {
            $table->id('id_alert')->comment('Identificador único de la alerta');
            $table->string('name_alert', 50)->unique()->comment('Nombre de la alerta');
            $table->text('description')->nullable()->comment('Descripción de la alerta');
            $table->decimal('threshold_km', 10, 2)->comment('Umbral de kilómetros para activar la alerta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_statuses');
    }
};
