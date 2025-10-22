<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use App\Models\Maintenance;
use App\Models\Delivery;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Gate;

class FleetStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    protected static bool $isLazy = false;
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Check authorization
        if (!Gate::allows('manage-fleet')) {
            return [];
        }

        // Total active vehicles
        $totalVehicles = Vehicle::where('status', true)->count();
        
        // Vehicles in operation today
        $vehiclesInOperation = \App\Models\DeliveryAssignment::whereDate('assignment_date', today())
            ->where('status', true)
            ->whereHas('delivery.deliveryStatus', function($query) {
                $query->where('name_status', 'En Ruta');
            })
            ->distinct('vehicle_id')
            ->count('vehicle_id');

        // Vehicles in maintenance
        $vehiclesInMaintenance = Maintenance::where('status', 'En Proceso')
            ->distinct('vehicle_id')
            ->count('vehicle_id');

        // Available vehicles
        $availableVehicles = $totalVehicles - $vehiclesInOperation - $vehiclesInMaintenance;

        // Fleet utilization rate
        $utilizationRate = $totalVehicles > 0 ? round(($vehiclesInOperation / $totalVehicles) * 100) : 0;

        return [
            Stat::make('Flota Total', $totalVehicles)
                ->description('Vehículos activos')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),

            Stat::make('En Operación', $vehiclesInOperation)
                ->description("{$utilizationRate}% utilización")
                ->descriptionIcon('heroicon-m-play')
                ->color('success'),

            Stat::make('En Mantenimiento', $vehiclesInMaintenance)
                ->description('No disponibles')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('warning'),

            Stat::make('Disponibles', $availableVehicles)
                ->description('Listos para asignar')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}