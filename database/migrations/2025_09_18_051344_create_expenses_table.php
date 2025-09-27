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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_expense_type')->constrained('expense_types');
            $table->foreignId('id_user')->constrained('users');
            $table->foreignId('id_vehicle')->nullable()->constrained('vehicles');
            $table->text('description');
            $table->decimal('amount', 10);
            $table->date('expense_date');
            $table->boolean('status')->nullable()->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
