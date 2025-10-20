<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_order')->comment('Identificador único del pedido');
            $table->foreignId('customer_id')->constrained('customers')->comment('Cliente que realizó el pedido');
            $table->dateTime('order_date')->useCurrent()->comment('Fecha y hora en que se realizó el pedido');
            $table->decimal('total_amount', 12, 2)->nullable()->comment('Monto total del pedido');
            $table->enum('status', ['PENDING', 'CONFIRMED', 'CANCELLED', 'DELIVERED'])->default('PENDING')->comment('Estado actual del pedido');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
