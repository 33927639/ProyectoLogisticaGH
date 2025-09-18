-- =============================================
-- Installation script for all stored procedures
-- Execute this script to install all logistics system procedures
-- =============================================

-- Set SQL mode for better compatibility
SET SQL_MODE = 'TRADITIONAL,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';

-- Use the logistic database
USE logistic;

-- Show current database
SELECT DATABASE() as current_database;

-- =============================================
-- 1. Install ActualizarKilometrajeCamion procedure
-- =============================================
SOURCE 01_actualizar_kilometraje_camion.sql;

-- =============================================
-- 2. Install VerificarMantenimientoPendiente procedure
-- =============================================
SOURCE 02_verificar_mantenimiento_pendiente.sql;

-- =============================================
-- 3. Install GenerarReporteViajes procedure
-- =============================================
SOURCE 03_generar_reporte_viajes.sql;

-- =============================================
-- Verify all procedures were created successfully
-- =============================================
SELECT 
    ROUTINE_NAME as procedure_name,
    ROUTINE_TYPE as type,
    CREATED as created_date,
    LAST_ALTERED as last_modified
FROM INFORMATION_SCHEMA.ROUTINES 
WHERE ROUTINE_SCHEMA = 'logistic' 
AND ROUTINE_TYPE = 'PROCEDURE'
AND ROUTINE_NAME IN ('ActualizarKilometrajeCamion', 'VerificarMantenimientoPendiente', 'GenerarReporteViajes')
ORDER BY ROUTINE_NAME;