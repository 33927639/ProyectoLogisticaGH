<?php

namespace App\Console\Commands;

use App\Services\KilometerService;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckMaintenanceNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:check-notifications {--vehicle-id= : ID específico de vehículo a revisar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisa y genera notificaciones de mantenimiento basadas en kilometraje';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Iniciando verificación de notificaciones de mantenimiento...');
        
        $vehicleId = $this->option('vehicle-id');
        
        try {
            if ($vehicleId) {
                $this->info("Verificando vehículo ID: {$vehicleId}");
                $notifications = KilometerService::checkMaintenanceNotifications($vehicleId);
                
                if (empty($notifications)) {
                    $this->info("✅ No se requieren notificaciones para el vehículo {$vehicleId}");
                } else {
                    $this->info("📬 Generadas " . count($notifications) . " notificaciones para vehículo {$vehicleId}");
                    foreach ($notifications as $notification) {
                        $this->line("   - {$notification['type']}: {$notification['message']}");
                    }
                }
            } else {
                $this->info('Verificando todos los vehículos...');
                
                // Generar notificaciones automáticas del sistema
                $totalNotifications = NotificationService::generateAutomaticNotifications();
                
                $this->info("✅ Proceso completado.");
                $this->info("📊 Total de notificaciones generadas: {$totalNotifications}");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error durante la verificación: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
