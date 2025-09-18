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
        Schema::table('mantemientos', function (Blueprint $table) {
            $table->foreign(['camion_id'])->references(['id'])->on('camiones')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mantemientos', function (Blueprint $table) {
            $table->dropForeign('mantemientos_camion_id_foreign');
        });
    }
};
