-- =============================================
-- Stored Procedure: VerificarMantenimientoPendiente
-- Description: Checks if a truck needs maintenance based on current mileage
-- Parameters: 
--   @camion_id: ID of the truck to check
-- =============================================

DELIMITER //

DROP PROCEDURE IF EXISTS VerificarMantenimientoPendiente;

CREATE PROCEDURE VerificarMantenimientoPendiente(
    IN p_camion_id INT
)
BEGIN
    DECLARE v_kilometraje_actual DECIMAL(10,2) DEFAULT 0;
    DECLARE v_intervalo_mantenimiento INT DEFAULT 5000;
    DECLARE v_ultimo_mantenimiento_km DECIMAL(10,2) DEFAULT 0;
    DECLARE v_km_desde_ultimo_mant DECIMAL(10,2) DEFAULT 0;
    DECLARE v_km_hasta_proximo_mant DECIMAL(10,2) DEFAULT 0;
    DECLARE v_mantenimiento_necesario BOOLEAN DEFAULT FALSE;
    DECLARE v_placa VARCHAR(255) DEFAULT '';
    DECLARE v_marca VARCHAR(255) DEFAULT '';
    DECLARE v_modelo VARCHAR(255) DEFAULT '';
    DECLARE v_estado_camion VARCHAR(50) DEFAULT '';
    DECLARE v_mantenimientos_pendientes INT DEFAULT 0;
    DECLARE v_error_message VARCHAR(255) DEFAULT '';
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            v_error_message = MESSAGE_TEXT;
        SELECT 
            'ERROR' as status,
            v_error_message as mensaje,
            NULL as camion_info,
            NULL as mantenimiento_info;
    END;

    -- Validate input parameters
    IF p_camion_id IS NULL OR p_camion_id <= 0 THEN
        SELECT 
            'ERROR' as status,
            'Invalid camion_id parameter' as mensaje,
            NULL as camion_info,
            NULL as mantenimiento_info;
    END IF;

    -- Get current truck data
    SELECT 
        kilometraje_actual, 
        intervalo_mantenimiento_km,
        placa,
        marca,
        modelo,
        estado
    INTO 
        v_kilometraje_actual, 
        v_intervalo_mantenimiento,
        v_placa,
        v_marca,
        v_modelo,
        v_estado_camion
    FROM camiones 
    WHERE id = p_camion_id;

    -- Check if truck exists
    IF v_kilometraje_actual IS NULL THEN
        SELECT 
            'ERROR' as status,
            'Truck not found' as mensaje,
            NULL as camion_info,
            NULL as mantenimiento_info;
    END IF;

    -- Get the mileage when last maintenance was completed
    SELECT COALESCE(
        (SELECT c2.kilometraje_actual 
         FROM mantemientos m2
         INNER JOIN camiones c2 ON m2.camion_id = c2.id
         WHERE m2.camion_id = p_camion_id 
         AND m2.estado = 'Completado'
         AND m2.fecha_realizada IS NOT NULL
         ORDER BY m2.fecha_realizada DESC, m2.id DESC
         LIMIT 1), 
        0
    ) INTO v_ultimo_mantenimiento_km;

    -- Calculate kilometers since last maintenance
    SET v_km_desde_ultimo_mant = v_kilometraje_actual - v_ultimo_mantenimiento_km;

    -- Calculate kilometers until next maintenance
    SET v_km_hasta_proximo_mant = v_intervalo_mantenimiento - v_km_desde_ultimo_mant;

    -- Determine if maintenance is needed
    SET v_mantenimiento_necesario = (v_km_desde_ultimo_mant >= v_intervalo_mantenimiento);

    -- Count pending maintenance records
    SELECT COUNT(*) 
    INTO v_mantenimientos_pendientes
    FROM mantemientos 
    WHERE camion_id = p_camion_id 
    AND estado IN ('Programado', 'En Proceso');

    -- Return comprehensive maintenance status
    SELECT 
        'SUCCESS' as status,
        'Verificación de mantenimiento completada' as mensaje,
        JSON_OBJECT(
            'camion_id', p_camion_id,
            'placa', v_placa,
            'marca', v_marca,
            'modelo', v_modelo,
            'estado', v_estado_camion,
            'kilometraje_actual', v_kilometraje_actual
        ) as camion_info,
        JSON_OBJECT(
            'mantenimiento_necesario', v_mantenimiento_necesario,
            'intervalo_mantenimiento_km', v_intervalo_mantenimiento,
            'ultimo_mantenimiento_km', v_ultimo_mantenimiento_km,
            'km_desde_ultimo_mantenimiento', v_km_desde_ultimo_mant,
            'km_hasta_proximo_mantenimiento', 
            CASE 
                WHEN v_mantenimiento_necesario THEN 0 
                ELSE v_km_hasta_proximo_mant 
            END,
            'mantenimientos_pendientes', v_mantenimientos_pendientes,
            'porcentaje_intervalo_usado', 
            ROUND((v_km_desde_ultimo_mant / v_intervalo_mantenimiento) * 100, 2),
            'alerta', 
            CASE 
                WHEN v_mantenimiento_necesario THEN 'URGENTE - Mantenimiento vencido'
                WHEN v_km_hasta_proximo_mant <= 500 THEN 'ADVERTENCIA - Mantenimiento próximo'
                WHEN v_km_hasta_proximo_mant <= 1000 THEN 'INFO - Programar mantenimiento pronto'
                ELSE 'OK - Mantenimiento al día'
            END
        ) as mantenimiento_info;

END //

DELIMITER ;