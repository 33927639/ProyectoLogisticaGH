<?php

namespace App\Jobs;

use App\Services\KilometerService;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessMaintenanceNotifications implements ShouldQueue
{
    use Queueable;

    public $vehicleId;

    /**
     * Create a new job instance.
     */
    public function __construct($vehicleId = null)
    {
        $this->vehicleId = $vehicleId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('🔧 Iniciando procesamiento de notificaciones de mantenimiento', [
                'vehicle_id' => $this->vehicleId
            ]);
            
            if ($this->vehicleId) {
                // Procesar vehículo específico
                $notifications = KilometerService::checkMaintenanceNotifications($this->vehicleId);
                
                Log::info('Notificaciones generadas para vehículo específico', [
                    'vehicle_id' => $this->vehicleId,
                    'notifications_count' => count($notifications)
                ]);
                
            } else {
                // Procesar todos los vehículos
                $totalNotifications = NotificationService::generateAutomaticNotifications();
                
                Log::info('Notificaciones automáticas generadas', [
                    'total_notifications' => $totalNotifications
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error procesando notificaciones de mantenimiento: ' . $e->getMessage(), [
                'vehicle_id' => $this->vehicleId,
                'exception' => $e
            ]);
            
            throw $e;
        }
    }
}
