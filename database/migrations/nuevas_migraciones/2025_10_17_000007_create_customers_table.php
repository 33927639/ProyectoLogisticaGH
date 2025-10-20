<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('id_customer')->comment('Identificador del cliente');
            $table->string('name', 150)->comment('Nombre completo o razón social');
            $table->string('nit', 20)->nullable()->comment('Número de identificación tributaria');
            $table->string('phone', 20)->nullable()->comment('Teléfono del cliente');
            $table->string('email', 100)->nullable()->comment('Correo electrónico del cliente');
            $table->text('address')->nullable()->comment('Dirección completa del cliente');
            $table->foreignId('municipality_id')->nullable()->constrained('municipalities')->comment('Municipio del cliente');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
