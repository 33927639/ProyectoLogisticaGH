<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;
use App\Models\TblDriver;

class DriversSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drivers = [
            [
                'name' => 'Juan Carlos López Morales',
                'license' => 'A-123456789',
                'status' => 1
            ],
            [
                'name' => 'Miguel Ángel Ramírez García',
                'license' => 'A-987654321',
                'status' => 1
            ],
            [
                'name' => 'Carlos Eduardo Mendoza Pérez',
                'license' => 'A-456789123',
                'status' => 1
            ],
            [
                'name' => 'Roberto Antonio Castillo Hernández',
                'license' => 'A-789123456',
                'status' => 1
            ],
            [
                'name' => 'José Luis Morales Díaz',
                'license' => 'A-321654987',
                'status' => 1
            ]
        ];

        foreach ($drivers as $driverData) {
            TblDriver::create(array_merge($driverData, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        $this->command->info('Conductores creados exitosamente: ' . count($drivers) . ' conductores agregados.');
        $this->command->info('Conductores disponibles:');
        foreach ($drivers as $driver) {
            $this->command->info("- {$driver['name']} (Licencia: {$driver['license']})");
        }
    }
}