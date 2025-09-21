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
        Schema::create('tbl_maintenances', function (Blueprint $table) {
            $table->integer('id_maintenance', true);
            $table->integer('id_vehicle')->index('fk__tbl_maint__id_ve__00200768');
            $table->string('type', 100);
            $table->date('maintenance_date');
            $table->boolean('approved')->nullable()->default(false);
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
        Schema::dropIfExists('tbl_maintenances');
    }
};
