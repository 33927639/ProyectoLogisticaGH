<?php

// Script para crear asignaciones de entrega de prueba
// Ejecutar con: php artisan tinker < create_assignments.php

use App\Models\DeliveryAssignment;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Vehicle;

echo "Creando asignaciones de entrega de prueba...\n";

// Obtener datos existentes
$deliveries = Delivery::take(3)->get();
$drivers = Driver::take(3)->get();
$vehicles = Vehicle::take(3)->get();

if ($deliveries->count() > 0 && $drivers->count() > 0 && $vehicles->count() > 0) {
    // Crear 3 asignaciones de prueba
    $assignments = [
        [
            'delivery_id' => $deliveries[0]->id_delivery,
            'driver_id' => $drivers[0]->id_driver,
            'vehicle_id' => $vehicles[0]->id_vehicle ?? 1,
            'assignment_date' => now()->format('Y-m-d'),
            'assigned_at' => now(),
            'driver_status' => 'pendiente',
            'notes' => 'Asignación de prueba 1'
        ],
        [
            'delivery_id' => $deliveries[1]->id_delivery ?? $deliveries[0]->id_delivery,
            'driver_id' => $drivers[1]->id_driver ?? $drivers[0]->id_driver,
            'vehicle_id' => $vehicles[1]->id_vehicle ?? 2,
            'assignment_date' => now()->format('Y-m-d'),
            'assigned_at' => now(),
            'driver_status' => 'pendiente',
            'notes' => 'Asignación de prueba 2'
        ],
        [
            'delivery_id' => $deliveries[2]->id_delivery ?? $deliveries[0]->id_delivery,
            'driver_id' => $drivers[2]->id_driver ?? $drivers[0]->id_driver,
            'vehicle_id' => $vehicles[2]->id_vehicle ?? 3,
            'assignment_date' => now()->format('Y-m-d'),
            'assigned_at' => now(),
            'driver_status' => 'pendiente',
            'notes' => 'Asignación de prueba 3'
        ]
    ];

    foreach ($assignments as $index => $assignmentData) {
        try {
            // Verificar si ya existe esta asignación
            $existing = DeliveryAssignment::where('delivery_id', $assignmentData['delivery_id'])
                                        ->where('driver_id', $assignmentData['driver_id'])
                                        ->where('vehicle_id', $assignmentData['vehicle_id'])
                                        ->first();

            if (!$existing) {
                $assignment = new DeliveryAssignment($assignmentData);
                $assignment->save();
                echo "✅ Asignación " . ($index + 1) . " creada exitosamente\n";
            } else {
                echo "⚠️  Asignación " . ($index + 1) . " ya existe\n";
            }
        } catch (Exception $e) {
            echo "❌ Error creando asignación " . ($index + 1) . ": " . $e->getMessage() . "\n";
        }
    }

    echo "\n🎯 ¡Asignaciones creadas! Ahora puedes:\n";
    echo "1. Ir a /conductor y hacer login\n";
    echo "2. Ver las entregas asignadas\n";
    echo "3. Probar las acciones: Aceptar → Iniciar Ruta → Completar\n";
    echo "4. Verificar en /admin que se actualicen en tiempo real\n";

} else {
    echo "❌ No hay suficientes datos (entregas, conductores, vehículos) para crear asignaciones\n";
    echo "Entregas: " . $deliveries->count() . "\n";
    echo "Conductores: " . $drivers->count() . "\n";
    echo "Vehículos: " . $vehicles->count() . "\n";
}

echo "\nScript completado.\n";