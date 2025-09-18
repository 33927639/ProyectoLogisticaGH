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
        Schema::table('tbl_delivery_guides', function (Blueprint $table) {
            $table->foreign(['id_delivery'], 'FK__tbl_deliv__id_de__75A278F5')->references(['id_delivery'])->on('tbl_deliveries')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_delivery_guides', function (Blueprint $table) {
            $table->dropForeign('FK__tbl_deliv__id_de__75A278F5');
        });
    }
};
