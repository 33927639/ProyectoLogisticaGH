<?php

namespace App\Filament\Widgets;

use App\Models\DeliveryAssignment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class RealTimeStatusWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '3s'; // Actualizar cada 3 segundos
    protected static bool $isLazy = false;
    protected static ?int $sort = 0; // Mostrar al inicio

    protected function getStats(): array
    {
        $now = Carbon::now();
        
        // Entregas que cambiaron de estado en los últimos 30 segundos
        $recentChanges = DeliveryAssignment::where('updated_at', '>=', $now->subSeconds(30))->count();
        
        // Conductores activos ahora mismo
        $activeDrivers = DeliveryAssignment::whereIn('driver_status', ['agarrado', 'en_ruta'])
            ->distinct('driver_id')
            ->count();
            
        // Entregas completadas en la última hora
        $recentCompletions = DeliveryAssignment::where('driver_status', 'completado')
            ->where('updated_at', '>=', $now->subHour())
            ->count();
            
        // Entregas iniciadas en la última hora  
        $recentStarts = DeliveryAssignment::whereIn('driver_status', ['agarrado', 'en_ruta'])
            ->where('updated_at', '>=', $now->subHour())
            ->count();

        return [
            Stat::make('🔴 Actividad Reciente', $recentChanges)
                ->description('Cambios en 30s')
                ->descriptionIcon('heroicon-o-bolt')
                ->color($recentChanges > 0 ? 'success' : 'gray'),

            Stat::make('👥 Conductores Activos', $activeDrivers)
                ->description('En servicio ahora')
                ->descriptionIcon('heroicon-o-user-group')
                ->color($activeDrivers > 0 ? 'primary' : 'gray'),

            Stat::make('✅ Completadas 1h', $recentCompletions)
                ->description('Entregas finalizadas')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('🚛 Iniciadas 1h', $recentStarts)
                ->description('Entregas en proceso')
                ->descriptionIcon('heroicon-o-truck')
                ->color('warning'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}