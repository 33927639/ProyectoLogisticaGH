<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('name');
            $table->string('sku')->unique();
            $table->decimal('price', 8, 2);
            $table->integer('stock');
            $table->text('description')->nullable();
            $table->date('expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name', 'sku', 'price', 'stock', 'description', 'expires_at']);
        });
    }
};
