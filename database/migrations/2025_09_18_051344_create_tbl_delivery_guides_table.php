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
        Schema::create('tbl_delivery_guides', function (Blueprint $table) {
            $table->integer('id_guide', true);
            $table->integer('id_delivery')->index('fk__tbl_deliv__id_de__75a278f5');
            $table->string('guide_number', 50)->unique('uq__tbl_deli__3b88fe57f23b3729');
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
        Schema::dropIfExists('tbl_delivery_guides');
    }
};
