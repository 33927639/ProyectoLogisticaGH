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
        Schema::table('order_products', function (Blueprint $table) {
            $table->integer('product_id')->nullable()->after('order_id');
            $table->index('product_id');
            
            // Foreign key constraint
            if (Schema::hasTable('products')) {
                $table->foreign('product_id')->references('id_product')->on('products')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            if (Schema::hasTable('products')) {
                $table->dropForeign(['product_id']);
            }
            $table->dropColumn('product_id');
        });
    }
};
