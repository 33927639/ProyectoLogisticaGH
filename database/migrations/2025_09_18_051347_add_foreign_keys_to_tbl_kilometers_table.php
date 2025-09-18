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
        Schema::table('tbl_kilometers', function (Blueprint $table) {
            $table->foreign(['id_alert'], 'FK__tbl_kilom__id_al__17F790F9')->references(['id_alert'])->on('tbl_alert_statuses')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_delivery'], 'FK__tbl_kilom__id_de__160F4887')->references(['id_delivery'])->on('tbl_deliveries')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_vehicle'], 'FK__tbl_kilom__id_ve__17036CC0')->references(['id_vehicle'])->on('tbl_vehicles')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_kilometers', function (Blueprint $table) {
            $table->dropForeign('FK__tbl_kilom__id_al__17F790F9');
            $table->dropForeign('FK__tbl_kilom__id_de__160F4887');
            $table->dropForeign('FK__tbl_kilom__id_ve__17036CC0');
        });
    }
};
