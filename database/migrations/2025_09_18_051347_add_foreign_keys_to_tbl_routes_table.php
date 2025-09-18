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
        Schema::table('tbl_routes', function (Blueprint $table) {
            $table->foreign(['id_destination'], 'FK__tbl_route__id_de__693CA210')->references(['id_municipality'])->on('tbl_municipalities')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_origin'], 'FK__tbl_route__id_or__68487DD7')->references(['id_municipality'])->on('tbl_municipalities')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_routes', function (Blueprint $table) {
            $table->dropForeign('FK__tbl_route__id_de__693CA210');
            $table->dropForeign('FK__tbl_route__id_or__68487DD7');
        });
    }
};
