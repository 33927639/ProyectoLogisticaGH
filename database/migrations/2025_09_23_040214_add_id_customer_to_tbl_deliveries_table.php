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
        Schema::table('tbl_deliveries', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_deliveries', 'id_customer')) {
                $table->unsignedBigInteger('id_customer')->nullable()->after('id_status');
                $table->foreign('id_customer')->references('id_customer')->on('tbl_customers')->onDelete('restrict')->onUpdate('restrict');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_deliveries', function (Blueprint $table) {
            $table->dropForeign(['id_customer']);
            $table->dropColumn('id_customer');
        });
    }
};
