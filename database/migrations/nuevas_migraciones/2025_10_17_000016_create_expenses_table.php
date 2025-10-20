<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id('id_expense')->comment('Identificador único del gasto');
            $table->foreignId('expense_type_id')->constrained('expense_types')->comment('Tipo de gasto');
            $table->foreignId('user_id')->constrained('users')->comment('Usuario que registró el gasto');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->comment('Vehículo asociado al gasto');
            $table->text('description')->comment('Descripción o detalle del gasto');
            $table->decimal('amount', 10, 2)->comment('Monto del gasto');
            $table->date('expense_date')->comment('Fecha del gasto');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
