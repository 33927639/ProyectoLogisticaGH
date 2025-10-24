<?php

namespace App\Filament\Driver\Widgets;

use App\Models\Delivery;
use App\Models\DeliveryAssignment;
use App\Models\Driver;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DriverStatsWidget extends BaseWidget
{
    /**
     * Helper para obtener el driver del usuario actual
     */
    protected function getCurrentDriver()
    {
        $user = auth('driver')->user();
        if (!$user) {
            return null;
        }
        $fullName = $user->first_name . ' ' . $user->last_name;
        return Driver::where('name', $fullName)->first();
    }

    protected function getStats(): array
    {
        $driver = $this->getCurrentDriver();
        
        if (!$driver) {
            return [
                Stat::make('Sin información', 'No se encontró información del conductor')
                    ->description('Contacte al administrador')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('danger'),
            ];
        }

        // Entregas pendientes de agarrar
        $pendingDeliveries = DeliveryAssignment::where('driver_id', $driver->id_driver)
            ->where('driver_status', 'pendiente')
            ->count();

        // Entregas agarradas
        $grabbedDeliveries = DeliveryAssignment::where('driver_id', $driver->id_driver)
            ->where('driver_status', 'agarrado')
            ->count();

        // Entregas en ruta
        $inRouteDeliveries = DeliveryAssignment::where('driver_id', $driver->id_driver)
            ->where('driver_status', 'en_ruta')
            ->count();

        // Entregas completadas hoy
        $completedToday = DeliveryAssignment::where('driver_id', $driver->id_driver)
            ->where('driver_status', 'completado')
            ->whereDate('updated_at', today())
            ->count();

        // Total de entregas completadas
        $totalCompleted = DeliveryAssignment::where('driver_id', $driver->id_driver)
            ->where('driver_status', 'completado')
            ->count();

        return [
            Stat::make('Pendientes', $pendingDeliveries)
                ->description('Entregas por agarrar')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Agarradas', $grabbedDeliveries)
                ->description('Listas para iniciar')
                ->descriptionIcon('heroicon-m-hand-raised')
                ->color('info'),
                
            Stat::make('En Ruta', $inRouteDeliveries)
                ->description('Entregas en progreso')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
                
            Stat::make('Completadas Hoy', $completedToday)
                ->description('Entregas finalizadas')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
                
            Stat::make('Total Completadas', $totalCompleted)
                ->description('Entregas históricas')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),
        ];
    }
}