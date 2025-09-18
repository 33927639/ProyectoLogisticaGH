-- =============================================
-- Test data and procedure testing script
-- =============================================

USE logistic;

-- Insert test data for trucks
INSERT INTO camiones (placa, marca, modelo, year, numero_motor, kilometraje_actual, intervalo_mantenimiento_km, estado) VALUES
('ABC-123', 'Volvo', 'FH16', 2020, 'VLV123456', 15000.00, 5000, 'Activo'),
('DEF-456', 'Mercedes', 'Actros', 2019, 'MER789012', 22000.00, 5000, 'Activo'),
('GHI-789', 'Scania', 'R450', 2021, 'SCA345678', 8000.00, 5000, 'Activo');

-- Insert test data for drivers
INSERT INTO pilotos (nombre, apellido, licencia, telefono, email, estado) VALUES
('Juan', 'Pérez', 'LIC001', '12345678', 'juan.perez@email.com', 'Activo'),
('María', 'González', 'LIC002', '87654321', 'maria.gonzalez@email.com', 'Activo'),
('Carlos', 'Rodríguez', 'LIC003', '11223344', 'carlos.rodriguez@email.com', 'Activo');

-- Insert test data for routes
INSERT INTO rutas (origen, destino, distancia_km, tiempo_estimado_horas, descripcion, estado) VALUES
('Guatemala City', 'Antigua', 45.5, 1.5, 'Ruta turística principal', 'Activa'),
('Guatemala City', 'Quetzaltenango', 205.0, 4.0, 'Ruta hacia el occidente', 'Activa'),
('Escuintla', 'Puerto San José', 35.0, 1.0, 'Ruta hacia la costa', 'Activa');

-- Insert test maintenance records
INSERT INTO mantemientos (camion_id, tipo_mantenimiento, descripcion, fecha_programada, fecha_realizada, costo, estado) VALUES
(1, 'Preventivo', 'Cambio de aceite y filtros', '2025-08-01', '2025-08-01', 500.00, 'Completado'),
(2, 'Correctivo', 'Reparación de frenos', '2025-08-15', '2025-08-16', 800.00, 'Completado');

-- Insert test trips
INSERT INTO viajes (camion_id, piloto_id, ruta_id, kilometraje_inicial, kilometraje_final, fecha_inicio, fecha_fin, estado) VALUES
(1, 1, 1, 15000.00, 15045.50, '2025-09-01 08:00:00', '2025-09-01 10:30:00', 'Completado'),
(2, 2, 2, 22000.00, 22205.00, '2025-09-02 06:00:00', '2025-09-02 11:00:00', 'Completado'),
(3, 3, 3, 8000.00, 8035.00, '2025-09-03 09:00:00', '2025-09-03 10:30:00', 'Completado'),
(1, 2, 2, 15045.50, NULL, '2025-09-04 07:00:00', NULL, 'En Curso');

-- =============================================
-- Test 1: ActualizarKilometrajeCamion
-- =============================================
SELECT '=== TEST 1: ActualizarKilometrajeCamion ===' as test_info;

-- Test updating truck mileage (should trigger maintenance for truck 1)
CALL ActualizarKilometrajeCamion(1, 1000.00);

-- Check the updated truck data
SELECT 'Truck data after update:' as info;
SELECT id, placa, kilometraje_actual, intervalo_mantenimiento_km FROM camiones WHERE id = 1;

-- Check if maintenance was created
SELECT 'Maintenance records for truck 1:' as info;
SELECT * FROM mantemientos WHERE camion_id = 1 ORDER BY created_at DESC LIMIT 1;

-- =============================================
-- Test 2: VerificarMantenimientoPendiente
-- =============================================
SELECT '=== TEST 2: VerificarMantenimientoPendiente ===' as test_info;

-- Test maintenance verification for truck 1
CALL VerificarMantenimientoPendiente(1);

-- Test maintenance verification for truck 2
CALL VerificarMantenimientoPendiente(2);

-- Test with invalid truck ID
CALL VerificarMantenimientoPendiente(999);

-- =============================================
-- Test 3: GenerarReporteViajes
-- =============================================
SELECT '=== TEST 3: GenerarReporteViajes ===' as test_info;

-- Generate report for September 2025
CALL GenerarReporteViajes('2025-09-01', '2025-09-05');

-- Test with invalid date range
CALL GenerarReporteViajes('2025-09-10', '2025-09-01');

SELECT '=== ALL TESTS COMPLETED ===' as test_info;