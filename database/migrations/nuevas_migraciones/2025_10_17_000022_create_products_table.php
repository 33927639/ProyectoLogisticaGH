<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('id_product')->comment('Identificador único del producto');
            $table->string('name', 150)->comment('Nombre o descripción del producto');
            $table->string('sku', 50)->unique()->nullable()->comment('Código o referencia SKU del producto');
            $table->text('description')->nullable()->comment('Descripción del producto');
            $table->decimal('unit_price', 10, 2)->nullable()->comment('Precio unitario del producto');
            $table->decimal('weight_kg', 10, 2)->nullable()->comment('Peso del producto en kilogramos');
            $table->decimal('volume_m3', 10, 3)->nullable()->comment('Volumen del producto en metros cúbicos');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
