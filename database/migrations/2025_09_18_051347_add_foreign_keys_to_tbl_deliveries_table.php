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
            $table->foreign(['id_route'], 'FK__tbl_deliv__id_ro__6EF57B66')->references(['id_route'])->on('tbl_routes')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_status'], 'FK__tbl_deliv__id_st__6FE99F9F')->references(['id_status'])->on('tbl_delivery_statuses')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_deliveries', function (Blueprint $table) {
            $table->dropForeign('FK__tbl_deliv__id_ro__6EF57B66');
            $table->dropForeign('FK__tbl_deliv__id_st__6FE99F9F');
        });
    }
};
