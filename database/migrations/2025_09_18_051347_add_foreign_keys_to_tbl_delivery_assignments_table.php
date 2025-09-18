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
        Schema::table('tbl_delivery_assignments', function (Blueprint $table) {
            $table->foreign(['id_delivery'], 'FK__tbl_deliv__id_de__787EE5A0')->references(['id_delivery'])->on('tbl_deliveries')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_driver'], 'FK__tbl_deliv__id_dr__7A672E12')->references(['id_driver'])->on('tbl_drivers')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_vehicle'], 'FK__tbl_deliv__id_ve__797309D9')->references(['id_vehicle'])->on('tbl_vehicles')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_delivery_assignments', function (Blueprint $table) {
            $table->dropForeign('FK__tbl_deliv__id_de__787EE5A0');
            $table->dropForeign('FK__tbl_deliv__id_dr__7A672E12');
            $table->dropForeign('FK__tbl_deliv__id_ve__797309D9');
        });
    }
};
