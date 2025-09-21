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
        Schema::create('tbl_vehicles', function (Blueprint $table) {
            $table->integer('id_vehicle', true);
            $table->string('license_plate', 20)->unique('uq__tbl_vehi__f72cd56e5c187a21');
            $table->integer('capacity');
            $table->string('plates', 20);
            $table->boolean('available')->default(true);
            $table->boolean('status')->nullable()->default(true);
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_vehicles');
    }
};
