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
        Schema::create('tbl_roles_has_users', function (Blueprint $table) {
            $table->integer('id_user');
            $table->integer('id_role')->index('fk__tbl_roles__id_ro__4ca06362');

            $table->primary(['id_user', 'id_role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_roles_has_users');
    }
};
