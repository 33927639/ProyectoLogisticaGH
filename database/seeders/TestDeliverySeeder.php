<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Delivery;
use App\Models\DeliveryAssignment;
use App\Models\Route;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\DeliveryStatus;

class TestDeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que existen rutas
        $routes = Route::take(3)->get();
        if ($routes->count() === 0) {
            $this->command->error('No hay rutas disponibles. Por favor, cree algunas rutas primero.');
            return;
        }

        // Verificar que existen conductores
        $drivers = Driver::take(2)->get();
        if ($drivers->count() === 0) {
            $this->command->error('No hay conductores disponibles.');
            return;
        }

        // Verificar que existen vehículos
        $vehicles = Vehicle::take(2)->get();
        if ($vehicles->count() === 0) {
            $this->command->error('No hay vehículos disponibles.');
            return;
        }

        // Obtener estados de entrega
        $pendingStatus = DeliveryStatus::where('name_status', 'Pendiente')->first();
        $inRouteStatus = DeliveryStatus::where('name_status', 'En Ruta')->first();

        if (!$pendingStatus || !$inRouteStatus) {
            $this->command->error('No se encontraron los estados de entrega necesarios.');
            return;
        }

        // Crear entregas de prueba
        $deliveries = [
            [
                'delivery_date' => now()->addDays(1),
                'route_id' => $routes[0]->id_route,
                'status_id' => $pendingStatus->id_status,
                'status' => true,
            ],
            [
                'delivery_date' => now()->addDays(2),
                'route_id' => $routes[1]->id_route ?? $routes[0]->id_route,
                'status_id' => $pendingStatus->id_status,
                'status' => true,
            ],
            [
                'delivery_date' => now()->addDays(3),
                'route_id' => $routes[2]->id_route ?? $routes[0]->id_route,
                'status_id' => $inRouteStatus->id_status,
                'status' => true,
            ],
        ];

        foreach ($deliveries as $index => $deliveryData) {
            $delivery = Delivery::create($deliveryData);
            
            // Asignar conductor y vehículo
            $driver = $drivers[$index % $drivers->count()];
            $vehicle = $vehicles[$index % $vehicles->count()];
            
            DeliveryAssignment::create([
                'delivery_id' => $delivery->id_delivery,
                'driver_id' => $driver->id_driver,
                'vehicle_id' => $vehicle->id_vehicle,
                'assignment_date' => now(),
                'status' => $index === 2 ? 1 : 0, // 0 = pendiente, 1 = en ruta/activo
                'driver_status' => $index === 2 ? 'en_ruta' : 'pendiente',
            ]);

            $this->command->info("Entrega #{$delivery->id_delivery} creada y asignada a {$driver->name}");
        }

        $this->command->info('Entregas de prueba creadas exitosamente!');
    }
}
