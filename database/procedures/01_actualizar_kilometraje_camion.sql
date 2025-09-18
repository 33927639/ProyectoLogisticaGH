-- =============================================
-- Stored Procedure: ActualizarKilometrajeCamion
-- Description: Updates truck mileage and creates maintenance record if needed
-- Parameters: 
--   @camion_id: ID of the truck
--   @kilometros_recorridos: kilometers traveled to add to current mileage
-- =============================================

DELIMITER //

DROP PROCEDURE IF EXISTS ActualizarKilometrajeCamion;

CREATE PROCEDURE ActualizarKilometrajeCamion(
    IN p_camion_id INT,
    IN p_kilometros_recorridos DECIMAL(10,2)
)
BEGIN
    DECLARE v_kilometraje_actual DECIMAL(10,2) DEFAULT 0;
    DECLARE v_nuevo_kilometraje DECIMAL(10,2) DEFAULT 0;
    DECLARE v_intervalo_mantenimiento INT DEFAULT 5000;
    DECLARE v_ultimo_mantenimiento_km DECIMAL(10,2) DEFAULT 0;
    DECLARE v_mantenimiento_necesario BOOLEAN DEFAULT FALSE;
    DECLARE v_error_message VARCHAR(255) DEFAULT '';
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        GET DIAGNOSTICS CONDITION 1
            v_error_message = MESSAGE_TEXT;
        RESIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = v_error_message;
    END;

    -- Start transaction
    START TRANSACTION;

    -- Validate input parameters
    IF p_camion_id IS NULL OR p_camion_id <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid camion_id parameter';
    END IF;

    IF p_kilometros_recorridos IS NULL OR p_kilometros_recorridos < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid kilometros_recorridos parameter';
    END IF;

    -- Get current truck data
    SELECT 
        kilometraje_actual, 
        intervalo_mantenimiento_km
    INTO 
        v_kilometraje_actual, 
        v_intervalo_mantenimiento
    FROM camiones 
    WHERE id = p_camion_id AND estado != 'Inactivo';

    -- Check if truck exists and is active
    IF v_kilometraje_actual IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Truck not found or inactive';
    END IF;

    -- Calculate new mileage
    SET v_nuevo_kilometraje = v_kilometraje_actual + p_kilometros_recorridos;

    -- Update truck mileage
    UPDATE camiones 
    SET 
        kilometraje_actual = v_nuevo_kilometraje,
        updated_at = NOW()
    WHERE id = p_camion_id;

    -- Check if maintenance is needed
    -- Get the mileage of the last completed maintenance
    SELECT COALESCE(MAX(c.kilometraje_actual - (v_nuevo_kilometraje - c.kilometraje_actual)), 0)
    INTO v_ultimo_mantenimiento_km
    FROM mantemientos m
    INNER JOIN camiones c ON m.camion_id = c.id
    WHERE m.camion_id = p_camion_id 
    AND m.estado = 'Completado'
    AND m.tipo_mantenimiento = 'Preventivo';

    -- If no previous maintenance found, use 0 as base
    IF v_ultimo_mantenimiento_km IS NULL THEN
        SET v_ultimo_mantenimiento_km = 0;
    END IF;

    -- Check if maintenance is due
    IF (v_nuevo_kilometraje - v_ultimo_mantenimiento_km) >= v_intervalo_mantenimiento THEN
        SET v_mantenimiento_necesario = TRUE;
        
        -- Create maintenance record only if one doesn't exist already
        IF NOT EXISTS (
            SELECT 1 FROM mantemientos 
            WHERE camion_id = p_camion_id 
            AND estado IN ('Programado', 'En Proceso')
            AND tipo_mantenimiento = 'Preventivo'
        ) THEN
            INSERT INTO mantemientos (
                camion_id,
                tipo_mantenimiento,
                descripcion,
                fecha_programada,
                estado,
                created_at,
                updated_at
            ) VALUES (
                p_camion_id,
                'Preventivo',
                CONCAT('Mantenimiento preventivo programado automáticamente. Kilometraje actual: ', v_nuevo_kilometraje, ' km'),
                CURDATE() + INTERVAL 7 DAY, -- Schedule for next week
                'Programado',
                NOW(),
                NOW()
            );
        END IF;
    END IF;

    -- Commit transaction
    COMMIT;

    -- Return success message with maintenance status
    SELECT 
        'SUCCESS' as status,
        p_camion_id as camion_id,
        v_kilometraje_actual as kilometraje_anterior,
        v_nuevo_kilometraje as kilometraje_actual,
        p_kilometros_recorridos as kilometros_agregados,
        v_mantenimiento_necesario as mantenimiento_necesario,
        CASE 
            WHEN v_mantenimiento_necesario THEN 
                CONCAT('Mantenimiento programado para el camión ID: ', p_camion_id)
            ELSE 
                'Kilometraje actualizado correctamente'
        END as mensaje;

END //

DELIMITER ;