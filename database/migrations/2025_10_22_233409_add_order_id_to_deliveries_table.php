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
        Schema::table('deliveries', function (Blueprint $table) {
            // Solo agregar campos que no existen
            if (!Schema::hasColumn('deliveries', 'order_id')) {
                $table->integer('order_id')->nullable()->after('id_delivery'); // Cambiar a int simple para coincidir con orders.id_order
            }
            if (!Schema::hasColumn('deliveries', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('delivery_date');
            }
            if (!Schema::hasColumn('deliveries', 'delivery_address')) {
                $table->string('delivery_address')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('deliveries', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->nullable()->after('delivery_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'customer_name', 'delivery_address', 'total_amount']);
        });
    }
};
