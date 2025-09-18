<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delivery_statuses_get_all`()
BEGIN
    SELECT id_status, name_status, description, status, created_at, updated_at
    FROM tbl_delivery_statuses
    WHERE status = 1; -- solo activos
END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_delivery_statuses_get_all");
    }
};
