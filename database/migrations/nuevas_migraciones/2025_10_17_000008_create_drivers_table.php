<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id('id_driver')->comment('Identificador único del conductor');
            $table->string('name', 100)->nullable()->comment('Nombre completo del conductor');
            $table->string('license', 50)->unique()->comment('Número de licencia de conducir');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
