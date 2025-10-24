<?php

namespace App\Filament\Widgets;

use App\Models\Delivery;
use App\Models\DeliveryStatus;
use App\Models\DeliveryAssignment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

class DeliveryStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '45s'; // ESTADÍSTICO: Estadísticas generales
    protected static bool $isLazy = false;
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Check authorization
        if (!Gate::allows('viewAny', Delivery::class)) {
            return [];
        }

        // Cache por 15 segundos para optimizar rendimiento
        return Cache::remember('delivery_stats_' . today()->format('Y-m-d'), 15, function () {
            $today = today();
            
            // Get delivery statuses
            $pendingStatus = DeliveryStatus::where('name_status', 'Pendiente')->first();
            $inRouteStatus = DeliveryStatus::where('name_status', 'En Ruta')->first();
            $deliveredStatus = DeliveryStatus::where('name_status', 'Entregado')->first();

            // Today's deliveries
            $todayDeliveries = Delivery::whereDate('delivery_date', $today);
            $totalToday = $todayDeliveries->count();
            
            // Stats from assignments (driver perspective)
            $todayAssignments = DeliveryAssignment::whereHas('delivery', function($query) use ($today) {
                $query->whereDate('delivery_date', $today);
            });
            
            $pendingDrivers = $todayAssignments->where('driver_status', 'pendiente')->count();
            $grabbedDrivers = $todayAssignments->where('driver_status', 'agarrado')->count();
            $inRouteDrivers = $todayAssignments->where('driver_status', 'en_ruta')->count();
            $completedDrivers = $todayAssignments->where('driver_status', 'completado')->count();

            // Calculate completion rate
            $totalAssignments = $pendingDrivers + $grabbedDrivers + $inRouteDrivers + $completedDrivers;
            $completionRate = $totalAssignments > 0 ? round(($completedDrivers / $totalAssignments) * 100) : 0;

            return [
                Stat::make('Entregas Hoy', $totalToday)
                    ->description('Total programadas')
                    ->descriptionIcon('heroicon-m-calendar-days')
                    ->color('primary'),

                Stat::make('Pendientes', $pendingDrivers)
                    ->description('Por aceptar')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color('gray'),

                Stat::make('En Proceso', $grabbedDrivers + $inRouteDrivers)
                    ->description("Agarradas: {$grabbedDrivers} | En Ruta: {$inRouteDrivers}")
                    ->descriptionIcon('heroicon-m-truck')
                    ->color($inRouteDrivers > 0 ? 'primary' : 'warning'),

                Stat::make('Completadas', $completedDrivers)
                    ->description("{$completionRate}% completado")
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color('success'),
            ];
        });
    }

    protected function getColumns(): int
    {
        return 4;
    }
}