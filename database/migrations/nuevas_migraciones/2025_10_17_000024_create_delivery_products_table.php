<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_products', function (Blueprint $table) {
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete()->comment('Entrega relacionada');
            $table->foreignId('product_id')->constrained('products')->comment('Producto entregado');
            $table->integer('quantity')->default(1)->comment('Cantidad de productos entregados');
            $table->decimal('unit_price', 10, 2)->nullable()->comment('Precio unitario del producto en la entrega');
            $table->decimal('subtotal', 12, 2)->storedAs('quantity * unit_price')->comment('Subtotal calculado automáticamente');
            $table->timestamps();

            $table->primary(['delivery_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_products');
    }
};
