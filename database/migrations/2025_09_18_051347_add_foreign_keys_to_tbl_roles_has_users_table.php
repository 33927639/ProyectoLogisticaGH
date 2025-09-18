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
        Schema::table('tbl_roles_has_users', function (Blueprint $table) {
            $table->foreign(['id_role'], 'FK__tbl_roles__id_ro__4CA06362')->references(['id_role'])->on('tbl_roles')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_user'], 'FK__tbl_roles__id_us__4BAC3F29')->references(['id_user'])->on('tbl_users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_roles_has_users', function (Blueprint $table) {
            $table->dropForeign('FK__tbl_roles__id_ro__4CA06362');
            $table->dropForeign('FK__tbl_roles__id_us__4BAC3F29');
        });
    }
};
