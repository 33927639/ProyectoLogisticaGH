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
        Schema::create('tbl_delivery_assignments', function (Blueprint $table) {
            $table->integer('id_delivery');
            $table->integer('id_vehicle')->index('fk__tbl_deliv__id_ve__797309d9');
            $table->integer('id_driver')->index('fk__tbl_deliv__id_dr__7a672e12');
            $table->date('assignment_date');

            $table->primary(['id_delivery', 'id_vehicle', 'id_driver']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_delivery_assignments');
    }
};
