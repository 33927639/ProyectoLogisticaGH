<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_logs', function (Blueprint $table) {
            $table->id('id_log')->comment('Identificador del registro de bitácora');
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete()->comment('Entrega a la que pertenece la acción');
            $table->foreignId('user_id')->constrained('users')->comment('Usuario que realizó la acción');
            $table->string('action', 100)->comment('Acción realizada sobre la entrega');
            $table->text('note')->nullable()->comment('Observaciones o comentarios adicionales');
            $table->timestamp('created_at')->useCurrent()->comment('Fecha y hora de registro');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_logs');
    }
};
