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
        Schema::create('tbl_municipalities', function (Blueprint $table) {
            $table->integer('id_municipality', true);
            $table->string('name_municipality', 100);
            $table->integer('id_department')->index('fk__tbl_munic__id_de__3e52440b');
            $table->boolean('status_municipality')->nullable()->default(true);
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_municipalities');
    }
};
