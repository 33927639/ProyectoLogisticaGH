<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->id('id_order_product');
            $table->integer('order_id'); // Cambiar a int simple para coincidir con orders.id_order
            $table->string('product_name');
            $table->string('product_description')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->string('unit')->default('unidad');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            // Foreign key
            if (Schema::hasTable('orders')) {
                $table->foreign('order_id')->references('id_order')->on('orders')->onDelete('cascade');
            }
            
            // Indexes
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
