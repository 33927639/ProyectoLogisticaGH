<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_statuses', function (Blueprint $table) {
            $table->id('id_status')->comment('Identificador del estado de entrega');
            $table->string('name_status', 50)->unique()->comment('Nombre del estado de entrega');
            $table->text('description')->nullable()->comment('Descripción o detalle del estado');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_statuses');
    }
};
