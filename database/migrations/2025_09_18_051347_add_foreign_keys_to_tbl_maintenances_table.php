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
        Schema::table('tbl_maintenances', function (Blueprint $table) {
            $table->foreign(['id_vehicle'], 'FK__tbl_maint__id_ve__00200768')->references(['id_vehicle'])->on('tbl_vehicles')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_maintenances', function (Blueprint $table) {
            $table->dropForeign('FK__tbl_maint__id_ve__00200768');
        });
    }
};
