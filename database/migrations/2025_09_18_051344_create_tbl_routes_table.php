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
        Schema::create('tbl_routes', function (Blueprint $table) {
            $table->integer('id_route', true);
            $table->integer('id_origin')->index('fk__tbl_route__id_or__68487dd7');
            $table->integer('id_destination')->index('fk__tbl_route__id_de__693ca210');
            $table->decimal('distance_km', 10);
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
        Schema::dropIfExists('tbl_routes');
    }
};
