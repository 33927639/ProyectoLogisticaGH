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
        Schema::create('tbl_incomes', function (Blueprint $table) {
            $table->integer('id_income', true);
            $table->decimal('amount', 10)->nullable();
            $table->text('description');
            $table->date('income_date')->default('now()');
            $table->integer('id_user')->index('fk__tbl_incom__id_us__1db06a4f');
            $table->integer('id_delivery')->nullable()->index('fk__tbl_incom__id_de__1ea48e88');
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
        Schema::dropIfExists('tbl_incomes');
    }
};
