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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delivery_statuses_update`(
    IN p_id_status INT,
    IN p_name_status VARCHAR(50),
    IN p_description TEXT,
    IN p_status BIT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE tbl_delivery_statuses
    SET name_status = p_name_status,
        description = p_description,
        status = p_status,
        updated_at = NOW()
    WHERE id_status = p_id_status;

    COMMIT;
END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_delivery_statuses_update");
    }
};
