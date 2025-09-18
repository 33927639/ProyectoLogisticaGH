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
        Schema::create('tbl_expenses', function (Blueprint $table) {
            $table->integer('id_expense', true);
            $table->integer('id_expense_type')->index('fk__tbl_expen__id_ex__0f624af8');
            $table->integer('id_user')->index('fk__tbl_expen__id_us__10566f31');
            $table->integer('id_vehicle')->nullable()->index('fk__tbl_expen__id_ve__114a936a');
            $table->text('description');
            $table->decimal('amount', 10);
            $table->date('expense_date');
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
        Schema::dropIfExists('tbl_expenses');
    }
};
