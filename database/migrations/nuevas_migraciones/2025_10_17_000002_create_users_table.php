<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user')->comment('Identificador único del usuario');
            $table->string('first_name', 100)->nullable()->comment('Primer nombre del usuario');
            $table->string('last_name', 100)->nullable()->comment('Apellido del usuario');
            $table->string('username', 100)->unique()->comment('Nombre de usuario único');
            $table->string('email', 150)->unique()->comment('Correo electrónico');
            $table->string('password', 255)->comment('Contraseña cifrada');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
