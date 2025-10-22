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
            $table->id('id_kilometer');
            $table->unsignedBigInteger('id_vehicle');
            $table->decimal('kilometers', 8, 2);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->timestamps();
            
            $table->foreign('id_vehicle')->references('id_vehicle')->on('tbl_vehicles');
            $table->foreign('id_user')->references('id_user')->on('tbl_users');
            
            $table->index(['id_vehicle', 'date']);
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
