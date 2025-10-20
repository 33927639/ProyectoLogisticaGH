<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles_users', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ID del usuario');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade')->comment('ID del rol asignado');
            $table->timestamps();

            $table->primary(['user_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles_users');
    }
};
