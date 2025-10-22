<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id('id_income')->comment('Identificador único del ingreso');
            $table->decimal('amount', 10, 2)->nullable()->comment('Monto del ingreso');
            $table->text('description')->comment('Descripción o detalle del ingreso');
            $table->dateTime('income_date')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('Fecha del ingreso');
            $table->foreignId('user_id')->constrained('users')->comment('Usuario que registró el ingreso');
            $table->foreignId('delivery_id')->nullable()->constrained('deliveries')->comment('Entrega asociada al ingreso, si aplica');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
