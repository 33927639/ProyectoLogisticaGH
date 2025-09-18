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
        Schema::create('tbl_alert_statuses', function (Blueprint $table) {
            $table->integer('id_alert', true);
            $table->string('name_alert', 50)->unique('uq__tbl_aler__deb047212a80a008');
            $table->text('description')->nullable();
            $table->decimal('threshold_km', 10);
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_alert_statuses');
    }
};
