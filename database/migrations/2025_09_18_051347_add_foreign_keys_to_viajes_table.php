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
        Schema::table('viajes', function (Blueprint $table) {
            $table->foreign(['camion_id'])->references(['id'])->on('camiones')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['piloto_id'])->references(['id'])->on('pilotos')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['ruta_id'])->references(['id'])->on('rutas')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viajes', function (Blueprint $table) {
            $table->dropForeign('viajes_camion_id_foreign');
            $table->dropForeign('viajes_piloto_id_foreign');
            $table->dropForeign('viajes_ruta_id_foreign');
        });
    }
};
