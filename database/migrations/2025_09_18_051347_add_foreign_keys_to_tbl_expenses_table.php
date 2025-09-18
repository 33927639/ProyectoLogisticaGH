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
        Schema::table('tbl_expenses', function (Blueprint $table) {
            $table->foreign(['id_expense_type'], 'FK__tbl_expen__id_ex__0F624AF8')->references(['id_expense_type'])->on('tbl_expense_types')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_user'], 'FK__tbl_expen__id_us__10566F31')->references(['id_user'])->on('tbl_users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_vehicle'], 'FK__tbl_expen__id_ve__114A936A')->references(['id_vehicle'])->on('tbl_vehicles')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_expenses', function (Blueprint $table) {
            $table->dropForeign('FK__tbl_expen__id_ex__0F624AF8');
            $table->dropForeign('FK__tbl_expen__id_us__10566F31');
            $table->dropForeign('FK__tbl_expen__id_ve__114A936A');
        });
    }
};
