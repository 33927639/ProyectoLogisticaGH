<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id_role')->comment('Identificador único del rol');
            $table->string('name_role', 100)->unique()->comment('Nombre del rol del sistema');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
