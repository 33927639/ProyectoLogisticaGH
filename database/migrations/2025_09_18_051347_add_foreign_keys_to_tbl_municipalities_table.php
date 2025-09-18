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
        Schema::table('tbl_municipalities', function (Blueprint $table) {
            $table->foreign(['id_department'], 'FK__tbl_munic__id_de__3E52440B')->references(['id_department'])->on('tbl_departments')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_municipalities', function (Blueprint $table) {
            $table->dropForeign('FK__tbl_munic__id_de__3E52440B');
        });
    }
};
