<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Asegurar columna (solo si no existe)
        Schema::table('tbl_deliveries', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_deliveries', 'id_customer')) {
                $table->unsignedInteger('id_customer')->nullable()->after('id_route');
            }
        });

        // 2) Asegurar índice (solo si no existe)
        if (!$this->indexExists('tbl_deliveries', 'idx_top_active')) {
            Schema::table('tbl_deliveries', function (Blueprint $table) {
                $table->index(['id_customer', 'delivery_status', 'delivery_date', 'status'], 'idx_top_active');
            });
        }

        // 3) Asegurar foreign key (solo si no existe)
        if (!$this->foreignKeyExists('tbl_deliveries', 'fk_deliveries_customer')) {
            Schema::table('tbl_deliveries', function (Blueprint $table) {
                $table->foreign('id_customer', 'fk_deliveries_customer')
                    ->references('id_customer')->on('tbl_customers')
                    ->restrictOnDelete()
                    ->restrictOnUpdate();
            });
        }
    }

    public function down(): void
    {
        // Quitar FK si existe
        if ($this->foreignKeyExists('tbl_deliveries', 'fk_deliveries_customer')) {
            Schema::table('tbl_deliveries', function (Blueprint $table) {
                $table->dropForeign('fk_deliveries_customer');
            });
        }

        // Quitar índice si existe
        if ($this->indexExists('tbl_deliveries', 'idx_top_active')) {
            Schema::table('tbl_deliveries', function (Blueprint $table) {
                $table->dropIndex('idx_top_active');
            });
        }

        // Quitar columna si existe
        if (Schema::hasColumn('tbl_deliveries', 'id_customer')) {
            Schema::table('tbl_deliveries', function (Blueprint $table) {
                $table->dropColumn('id_customer');
            });
        }
    }

    // ===== helpers =====

    private function indexExists(string $table, string $indexName): bool
    {
        $db = DB::getDatabaseName();
        $result = DB::selectOne("
            SELECT 1
            FROM information_schema.statistics
            WHERE table_schema = ? AND table_name = ? AND index_name = ?
            LIMIT 1
        ", [$db, $table, $indexName]);

        return (bool) $result;
    }

    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $db = DB::getDatabaseName();
        $result = DB::selectOne("
            SELECT 1
            FROM information_schema.table_constraints
            WHERE constraint_schema = ? AND table_name = ? AND constraint_name = ? AND constraint_type = 'FOREIGN KEY'
            LIMIT 1
        ", [$db, $table, $constraintName]);

        return (bool) $result;
    }
};
