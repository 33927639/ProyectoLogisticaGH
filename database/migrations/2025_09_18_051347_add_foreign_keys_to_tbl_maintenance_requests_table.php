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
        Schema::table('tbl_maintenance_requests', function (Blueprint $table) {
            $table->foreign(['approved_by'], 'FK__tbl_maint__appro__05D8E0BE')->references(['id_user'])->on('tbl_users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_vehicle'], 'FK__tbl_maint__id_ve__04E4BC85')->references(['id_vehicle'])->on('tbl_vehicles')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_maintenance_requests', function (Blueprint $table) {
            $table->dropForeign('FK__tbl_maint__appro__05D8E0BE');
            $table->dropForeign('FK__tbl_maint__id_ve__04E4BC85');
        });
    }
};
