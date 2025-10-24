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
        Schema::table('routes', function (Blueprint $table) {
            $table->integer('estimated_duration')->nullable()->after('distance_km')->comment('Tiempo estimado en minutos');
            $table->decimal('total_distance', 8, 2)->nullable()->after('estimated_duration')->comment('Distancia total calculada en km');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn(['estimated_duration', 'total_distance']);
        });
    }
};
