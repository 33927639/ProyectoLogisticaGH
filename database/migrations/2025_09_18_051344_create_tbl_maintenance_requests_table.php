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
        Schema::create('tbl_maintenance_requests', function (Blueprint $table) {
            $table->integer('id_request', true);
            $table->integer('id_vehicle')->index('fk__tbl_maint__id_ve__04e4bc85');
            $table->date('request_date');
            $table->text('reason');
            $table->integer('approved_by')->nullable()->index('fk__tbl_maint__appro__05d8e0be');
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
        Schema::dropIfExists('tbl_maintenance_requests');
    }
};
