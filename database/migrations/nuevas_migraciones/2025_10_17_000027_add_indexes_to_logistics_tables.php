<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Índices para optimizar reportes y consultas por fecha o estado
        Schema::table('customers', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_customers_status_created');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_deliveries_status_created');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_expenses_status_created');
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_incomes_status_created');
        });

        Schema::table('maintenances', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_maintenances_status_created');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_orders_status_created');
        });

        Schema::table('vehicle_tracking', function (Blueprint $table) {
            $table->index(['vehicle_id', 'recorded_at'], 'idx_tracking_vehicle_time');
        });
    }

    public function down(): void
    {
        Schema::table('customers', fn(Blueprint $t) => $t->dropIndex('idx_customers_status_created'));
        Schema::table('deliveries', fn(Blueprint $t) => $t->dropIndex('idx_deliveries_status_created'));
        Schema::table('expenses', fn(Blueprint $t) => $t->dropIndex('idx_expenses_status_created'));
        Schema::table('incomes', fn(Blueprint $t) => $t->dropIndex('idx_incomes_status_created'));
        Schema::table('maintenances', fn(Blueprint $t) => $t->dropIndex('idx_maintenances_status_created'));
        Schema::table('orders', fn(Blueprint $t) => $t->dropIndex('idx_orders_status_created'));
        Schema::table('vehicle_tracking', fn(Blueprint $t) => $t->dropIndex('idx_tracking_vehicle_time'));
    }
};
