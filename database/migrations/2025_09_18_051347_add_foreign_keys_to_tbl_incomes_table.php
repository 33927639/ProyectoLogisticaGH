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
        Schema::table('tbl_incomes', function (Blueprint $table) {
            $table->foreign(['id_delivery'], 'FK__tbl_incom__id_de__1EA48E88')->references(['id_delivery'])->on('tbl_deliveries')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_user'], 'FK__tbl_incom__id_us__1DB06A4F')->references(['id_user'])->on('tbl_users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_incomes', function (Blueprint $table) {
            $table->dropForeign('FK__tbl_incom__id_de__1EA48E88');
            $table->dropForeign('FK__tbl_incom__id_us__1DB06A4F');
        });
    }
};
