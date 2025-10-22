<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_tokens', function (Blueprint $table) {
            $table->id('id_token')->comment('Identificador del token');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Usuario propietario del token');
            $table->char('token_hash', 64)->comment('Hash del JWT token');
            $table->dateTime('expires_at')->comment('Fecha y hora de expiración');
            $table->boolean('revoked')->default(false)->comment('Indica si el token fue revocado');
            $table->timestamp('created_at')->useCurrent()->comment('Fecha de creación');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tokens');
    }
};
