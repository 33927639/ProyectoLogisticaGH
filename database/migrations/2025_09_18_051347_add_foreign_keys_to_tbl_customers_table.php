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
        Schema::table('tbl_customers', function (Blueprint $table) {
            $table->foreign(['id_municipality'], 'FK__tbl_custo__id_mu__5165187F')->references(['id_municipality'])->on('tbl_municipalities')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_customers', function (Blueprint $table) {
            $table->dropForeign('FK__tbl_custo__id_mu__5165187F');
        });
    }
};
