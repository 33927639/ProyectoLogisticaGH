-- =============================================
-- Stored Procedure: GenerarReporteViajes
-- Description: Generates a comprehensive trip report for a date range
-- Parameters: 
--   @fecha_inicio: Start date for the report
--   @fecha_fin: End date for the report
-- =============================================

DELIMITER //

DROP PROCEDURE IF EXISTS GenerarReporteViajes;

CREATE PROCEDURE GenerarReporteViajes(
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE
)
BEGIN
    DECLARE v_error_message VARCHAR(255) DEFAULT '';
    DECLARE v_total_viajes INT DEFAULT 0;
    DECLARE v_total_km_recorridos DECIMAL(12,2) DEFAULT 0;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            v_error_message = MESSAGE_TEXT;
        SELECT 
            'ERROR' as status,
            v_error_message as mensaje;
    END;

    -- Validate input parameters
    IF p_fecha_inicio IS NULL OR p_fecha_fin IS NULL THEN
        SELECT 
            'ERROR' as status,
            'Both fecha_inicio and fecha_fin parameters are required' as mensaje;
    END IF;

    IF p_fecha_inicio > p_fecha_fin THEN
        SELECT 
            'ERROR' as status,
            'fecha_inicio cannot be greater than fecha_fin' as mensaje;
    END IF;

    -- Get summary statistics
    SELECT 
        COUNT(*),
        COALESCE(SUM(CASE WHEN kilometraje_final IS NOT NULL AND kilometraje_inicial IS NOT NULL 
                          THEN kilometraje_final - kilometraje_inicial 
                          ELSE 0 END), 0)
    INTO v_total_viajes, v_total_km_recorridos
    FROM viajes
    WHERE DATE(fecha_inicio) BETWEEN p_fecha_inicio AND p_fecha_fin;

    -- Return comprehensive trip report
    SELECT 
        'SUCCESS' as status,
        'Reporte generado exitosamente' as mensaje,
        JSON_OBJECT(
            'periodo', JSON_OBJECT(
                'fecha_inicio', p_fecha_inicio,
                'fecha_fin', p_fecha_fin,
                'dias_periodo', DATEDIFF(p_fecha_fin, p_fecha_inicio) + 1
            ),
            'resumen', JSON_OBJECT(
                'total_viajes', v_total_viajes,
                'total_km_recorridos', v_total_km_recorridos,
                'promedio_km_por_viaje', 
                CASE WHEN v_total_viajes > 0 THEN ROUND(v_total_km_recorridos / v_total_viajes, 2) ELSE 0 END
            )
        ) as resumen_periodo;

    -- Detailed trip information
    SELECT 
        v.id as viaje_id,
        v.estado as viaje_estado,
        DATE(v.fecha_inicio) as fecha_viaje,
        TIME(v.fecha_inicio) as hora_inicio,
        CASE WHEN v.fecha_fin IS NOT NULL THEN TIME(v.fecha_fin) ELSE NULL END as hora_fin,
        
        -- Truck information
        c.id as camion_id,
        c.placa as camion_placa,
        CONCAT(c.marca, ' ', c.modelo, ' (', c.year, ')') as camion_info,
        c.estado as camion_estado,
        
        -- Driver information
        p.id as piloto_id,
        CONCAT(p.nombre, ' ', p.apellido) as piloto_nombre,
        p.licencia as piloto_licencia,
        p.telefono as piloto_telefono,
        
        -- Route information
        r.id as ruta_id,
        CONCAT(r.origen, ' → ', r.destino) as ruta_descripcion,
        r.distancia_km as ruta_distancia_km,
        r.tiempo_estimado_horas as ruta_tiempo_estimado,
        
        -- Trip details
        v.kilometraje_inicial,
        v.kilometraje_final,
        CASE 
            WHEN v.kilometraje_final IS NOT NULL AND v.kilometraje_inicial IS NOT NULL 
            THEN v.kilometraje_final - v.kilometraje_inicial 
            ELSE NULL 
        END as km_recorridos,
        
        -- Time calculations
        CASE 
            WHEN v.fecha_fin IS NOT NULL AND v.fecha_inicio IS NOT NULL 
            THEN TIMESTAMPDIFF(HOUR, v.fecha_inicio, v.fecha_fin)
            ELSE NULL 
        END as horas_viaje,
        
        -- Performance indicators
        CASE 
            WHEN v.kilometraje_final IS NOT NULL AND v.kilometraje_inicial IS NOT NULL 
                 AND r.distancia_km IS NOT NULL
            THEN 
                CASE 
                    WHEN ABS((v.kilometraje_final - v.kilometraje_inicial) - r.distancia_km) <= 10 
                    THEN 'Normal'
                    WHEN (v.kilometraje_final - v.kilometraje_inicial) > r.distancia_km + 10 
                    THEN 'Exceso de kilometraje'
                    ELSE 'Kilometraje insuficiente'
                END
            ELSE 'No evaluable'
        END as evaluacion_kilometraje,
        
        v.created_at as fecha_creacion,
        v.updated_at as fecha_actualizacion

    FROM viajes v
    INNER JOIN camiones c ON v.camion_id = c.id
    INNER JOIN pilotos p ON v.piloto_id = p.id
    INNER JOIN rutas r ON v.ruta_id = r.id
    WHERE DATE(v.fecha_inicio) BETWEEN p_fecha_inicio AND p_fecha_fin
    ORDER BY v.fecha_inicio DESC, v.id DESC;

    -- Summary by status
    SELECT 
        'ESTADISTICAS_POR_ESTADO' as tipo_reporte,
        v.estado,
        COUNT(*) as cantidad_viajes,
        COALESCE(SUM(CASE WHEN v.kilometraje_final IS NOT NULL AND v.kilometraje_inicial IS NOT NULL 
                          THEN v.kilometraje_final - v.kilometraje_inicial 
                          ELSE 0 END), 0) as total_km,
        COALESCE(AVG(CASE WHEN v.kilometraje_final IS NOT NULL AND v.kilometraje_inicial IS NOT NULL 
                          THEN v.kilometraje_final - v.kilometraje_inicial 
                          ELSE NULL END), 0) as promedio_km
    FROM viajes v
    WHERE DATE(v.fecha_inicio) BETWEEN p_fecha_inicio AND p_fecha_fin
    GROUP BY v.estado
    ORDER BY cantidad_viajes DESC;

    -- Summary by truck
    SELECT 
        'ESTADISTICAS_POR_CAMION' as tipo_reporte,
        c.id as camion_id,
        c.placa,
        CONCAT(c.marca, ' ', c.modelo) as camion_info,
        COUNT(v.id) as cantidad_viajes,
        COALESCE(SUM(CASE WHEN v.kilometraje_final IS NOT NULL AND v.kilometraje_inicial IS NOT NULL 
                          THEN v.kilometraje_final - v.kilometraje_inicial 
                          ELSE 0 END), 0) as total_km_recorridos,
        c.kilometraje_actual as kilometraje_actual_camion
    FROM camiones c
    LEFT JOIN viajes v ON c.id = v.camion_id 
                      AND DATE(v.fecha_inicio) BETWEEN p_fecha_inicio AND p_fecha_fin
    GROUP BY c.id, c.placa, c.marca, c.modelo, c.kilometraje_actual
    HAVING cantidad_viajes > 0
    ORDER BY total_km_recorridos DESC;

    -- Summary by driver
    SELECT 
        'ESTADISTICAS_POR_PILOTO' as tipo_reporte,
        p.id as piloto_id,
        CONCAT(p.nombre, ' ', p.apellido) as piloto_nombre,
        p.licencia,
        COUNT(v.id) as cantidad_viajes,
        COALESCE(SUM(CASE WHEN v.kilometraje_final IS NOT NULL AND v.kilometraje_inicial IS NOT NULL 
                          THEN v.kilometraje_final - v.kilometraje_inicial 
                          ELSE 0 END), 0) as total_km_conducidos,
        COALESCE(AVG(CASE WHEN v.fecha_fin IS NOT NULL AND v.fecha_inicio IS NOT NULL 
                          THEN TIMESTAMPDIFF(HOUR, v.fecha_inicio, v.fecha_fin)
                          ELSE NULL END), 0) as promedio_horas_por_viaje
    FROM pilotos p
    LEFT JOIN viajes v ON p.id = v.piloto_id 
                      AND DATE(v.fecha_inicio) BETWEEN p_fecha_inicio AND p_fecha_fin
    WHERE p.estado = 'Activo'
    GROUP BY p.id, p.nombre, p.apellido, p.licencia
    HAVING cantidad_viajes > 0
    ORDER BY total_km_conducidos DESC;

END //

DELIMITER ;