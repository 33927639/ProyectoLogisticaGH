<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MaintenanceAlertsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '3s'; // CRÍTICO: Alertas de mantenimiento
    protected static bool $isLazy = false;
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Vehículos que necesitan mantenimiento inmediato (>= 10,000 km)
        $criticalVehicles = Vehicle::where('total_kilometers', '>=', 10000)->count();
        
        // Vehículos próximos a mantenimiento (9,500 - 9,999 km)
        $warningVehicles = Vehicle::whereBetween('total_kilometers', [9500, 9999.99])->count();
        
        // Vehículos que se acercan (9,000 - 9,499 km)
        $infoVehicles = Vehicle::whereBetween('total_kilometers', [9000, 9499.99])->count();
        
        // Total de vehículos activos
        $totalVehicles = Vehicle::where('status', true)->count();

        return [
            Stat::make('🔴 Mantenimiento URGENTE', $criticalVehicles)
                ->description('Vehículos >= 10,000 km')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($criticalVehicles > 0 ? 'danger' : 'success')
                ->extraAttributes($criticalVehicles > 0 ? ['class' => 'animate-pulse'] : []),

            Stat::make('🟡 Próximo Mantenimiento', $warningVehicles)
                ->description('Vehículos 9,500-9,999 km')
                ->descriptionIcon('heroicon-o-clock')
                ->color($warningVehicles > 0 ? 'warning' : 'gray'),

            Stat::make('🔵 Se Acercan', $infoVehicles)
                ->description('Vehículos 9,000-9,499 km')
                ->descriptionIcon('heroicon-o-information-circle')
                ->color($infoVehicles > 0 ? 'info' : 'gray'),

            Stat::make('✅ Flota Total', $totalVehicles)
                ->description('Vehículos activos')
                ->descriptionIcon('heroicon-o-truck')
                ->color('primary'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}