<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliveryAssignment;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Vehicle;
use Carbon\Carbon;

class TestAssignmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🚀 Creando asignaciones de entrega de prueba...\n";

        // Obtener datos existentes
        $deliveries = Delivery::take(3)->get();
        $drivers = Driver::take(3)->get();
        $vehicles = Vehicle::take(3)->get();

        if ($deliveries->count() == 0) {
            echo "❌ No hay entregas en la base de datos\n";
            return;
        }

        if ($drivers->count() == 0) {
            echo "❌ No hay conductores en la base de datos\n";
            return;
        }

        $vehicleId = $vehicles->count() > 0 ? $vehicles->first()->id_vehicle : 1;

        // Crear asignaciones de prueba
        $assignments = [
            [
                'delivery_id' => $deliveries->first()->id_delivery,
                'driver_id' => $drivers->first()->id_driver,
                'vehicle_id' => $vehicleId,
                'assignment_date' => now()->format('Y-m-d'),
                'assigned_at' => now(),
                'driver_status' => 'pendiente',
                'notes' => 'Asignación de prueba para testing'
            ]
        ];

        // Si hay más datos, agregar más asignaciones
        if ($deliveries->count() > 1 && $drivers->count() > 1) {
            $assignments[] = [
                'delivery_id' => $deliveries->skip(1)->first()->id_delivery,
                'driver_id' => $drivers->skip(1)->first()->id_driver,
                'vehicle_id' => $vehicleId,
                'assignment_date' => now()->format('Y-m-d'),
                'assigned_at' => now(),
                'driver_status' => 'pendiente',
                'notes' => 'Segunda asignación de prueba'
            ];
        }

        foreach ($assignments as $index => $assignmentData) {
            try {
                // Verificar si ya existe
                $existing = DeliveryAssignment::where('delivery_id', $assignmentData['delivery_id'])
                                            ->where('driver_id', $assignmentData['driver_id'])
                                            ->where('vehicle_id', $assignmentData['vehicle_id'])
                                            ->first();

                if (!$existing) {
                    // Crear usando SQL directo para evitar problemas con clave primaria compuesta
                    \DB::table('delivery_assignments')->insert($assignmentData);
                    echo "✅ Asignación " . ($index + 1) . " creada exitosamente\n";
                    echo "   Entrega ID: {$assignmentData['delivery_id']}, Conductor ID: {$assignmentData['driver_id']}\n";
                } else {
                    echo "⚠️  Asignación " . ($index + 1) . " ya existe\n";
                }
            } catch (\Exception $e) {
                echo "❌ Error creando asignación " . ($index + 1) . ": " . $e->getMessage() . "\n";
            }
        }

        echo "\n🎯 ¡Proceso completado!\n";
        echo "Ahora puedes:\n";
        echo "1. Ir a /conductor y hacer login\n";
        echo "2. Ver las entregas asignadas\n";
        echo "3. Probar: Aceptar → Iniciar Ruta → Completar\n";
        echo "4. Verificar en /admin los cambios en tiempo real\n\n";
    }
}