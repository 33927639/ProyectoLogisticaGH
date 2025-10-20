<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('municipalities', function (Blueprint $table) {
            $table->id('id_municipality')->comment('Identificador del municipio');
            $table->string('name_municipality', 100)->comment('Nombre del municipio');
            $table->foreignId('department_id')->constrained('departments')->comment('Departamento al que pertenece');
            $table->tinyInteger('status')->default(1)->comment('1 activo, 0 inactivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipalities');
    }
};
