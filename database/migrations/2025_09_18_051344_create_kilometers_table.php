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
        Schema::create('kilometers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_delivery')->constrained('deliveries');
            $table->foreignId('id_vehicle')->constrained('vehicles');
            $table->decimal('kilometers_traveled', 10, 2);
            $table->foreignId('id_alert')->nullable()->constrained('alert_statuses');
            $table->date('record_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kilometers');
    }
};
