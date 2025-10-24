<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;
use App\Models\TblAlertStatus;

class AlertStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alertStatuses = [
            [
                'name_alert' => 'Mantenimiento 5,000 km',
                'description' => 'Mantenimiento preventivo cada 5,000 km',
                'threshold_km' => 5000.00
            ],
            [
                'name_alert' => 'Mantenimiento 10,000 km',
                'description' => 'Mantenimiento mayor cada 10,000 km',
                'threshold_km' => 10000.00
            ],
            [
                'name_alert' => 'Cambio de aceite',
                'description' => 'Cambio de aceite cada 3,000 km',
                'threshold_km' => 3000.00
            ],
            [
                'name_alert' => 'Revisión general',
                'description' => 'Revisión completa cada 15,000 km',
                'threshold_km' => 15000.00
            ],
            [
                'name_alert' => 'Inspección de frenos',
                'description' => 'Revisión de sistema de frenos cada 8,000 km',
                'threshold_km' => 8000.00
            ],
            [
                'name_alert' => 'Cambio de filtros',
                'description' => 'Cambio de filtros de aire y combustible cada 6,000 km',
                'threshold_km' => 6000.00
            ]
        ];

        foreach ($alertStatuses as $alertData) {
            TblAlertStatus::create(array_merge($alertData, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        $this->command->info('Estados de alertas creados exitosamente: ' . count($alertStatuses) . ' alertas agregadas.');
        $this->command->info('Alertas de mantenimiento disponibles:');
        foreach ($alertStatuses as $alert) {
            $this->command->info("- {$alert['name_alert']} ({$alert['threshold_km']} km)");
        }
    }
}