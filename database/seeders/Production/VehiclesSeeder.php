<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;
use App\Models\TblVehicle;

class VehiclesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'license_plate' => 'P-501ABC',
                'capacity' => 5000, // kg
                'available' => 1,
                'status' => 1
            ],
            [
                'license_plate' => 'C-205XYZ',
                'capacity' => 3500, // kg
                'available' => 1,
                'status' => 1
            ],
            [
                'license_plate' => 'M-750DEF',
                'capacity' => 1200, // kg
                'available' => 1,
                'status' => 1
            ],
            [
                'license_plate' => 'T-890GHI',
                'capacity' => 8000, // kg
                'available' => 1,
                'status' => 1
            ],
            [
                'license_plate' => 'F-456JKL',
                'capacity' => 2000, // kg
                'available' => 1,
                'status' => 1
            ]
        ];

        foreach ($vehicles as $vehicleData) {
            TblVehicle::create(array_merge($vehicleData, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        $this->command->info('Vehículos creados exitosamente: ' . count($vehicles) . ' vehículos agregados.');
        $this->command->info('Vehículos disponibles:');
        foreach ($vehicles as $vehicle) {
            $this->command->info("- {$vehicle['license_plate']} (Capacidad: {$vehicle['capacity']} kg)");
        }
    }
}