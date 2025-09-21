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
            $table->date('income_date');
            $table->foreignId('id_user')->constrained('users');
            $table->foreignId('id_deliverie')->constrained('tbl_deliveries');
            $table->boolean('status')->nullable()->default(true);
            $table->timestamps();
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
