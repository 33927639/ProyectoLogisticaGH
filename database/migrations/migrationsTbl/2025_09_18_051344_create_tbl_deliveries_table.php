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
        Schema::create('tbl_deliveries', function (Blueprint $table) {
            $table->id();
            $table->date('delivery_date');
            $table->string('delivery_status', 50);
            $table->integer('id_route')->index('fk__tbl_deliv__id_ro__6ef57b66');
            $table->integer('id_status')->default(1)->index('fk__tbl_deliv__id_st__6fe99f9f');
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
        Schema::dropIfExists('tbl_deliveries');
    }
};
