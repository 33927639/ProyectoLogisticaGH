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
        Schema::create('tbl_departments', function (Blueprint $table) {
            $table->integer('id_department', true);
            $table->string('name_department', 100)->unique('uq__tbl_depa__585ca65bdc9bca22');
            $table->boolean('status_department')->nullable()->default(true);
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_departments');
    }
};
