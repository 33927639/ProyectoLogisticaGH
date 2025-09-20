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
        Schema::create('tbl_kilometers', function (Blueprint $table) {
            $table->integer('id_kilometer', true);
            $table->integer('id_delivery')->index('fk__tbl_kilom__id_de__160f4887');
            $table->integer('id_vehicle')->index('fk__tbl_kilom__id_ve__17036cc0');
            $table->decimal('kilometers_traveled', 10);
            $table->integer('id_alert')->nullable()->index('fk__tbl_kilom__id_al__17f790f9');
            $table->date('record_date');
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
        Schema::dropIfExists('tbl_kilometers');
    }
};
