<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_guides', function (Blueprint $table) {
            $table->id('id_guide')->comment('Identificador de la guía de entrega');
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete()->comment('Entrega a la que pertenece');
            $table->string('guide_number', 50)->unique()->comment('Número de guía de entrega');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_guides');
    }
};
