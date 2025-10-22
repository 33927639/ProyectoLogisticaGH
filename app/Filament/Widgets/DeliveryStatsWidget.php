<?php

namespace App\Filament\Widgets;

use App\Models\Delivery;
use App\Models\DeliveryStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Gate;

class DeliveryStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected static bool $isLazy = false;
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Check authorization
        if (!Gate::allows('viewAny', Delivery::class)) {
            return [];
        }

        $today = today();
        
        // Get delivery statuses
        $pendingStatus = DeliveryStatus::where('name_status', 'Pendiente')->first();
        $inRouteStatus = DeliveryStatus::where('name_status', 'En Ruta')->first();
        $deliveredStatus = DeliveryStatus::where('name_status', 'Entregado')->first();

        // Today's deliveries
        $todayDeliveries = Delivery::whereDate('delivery_date', $today);
        $totalToday = $todayDeliveries->count();
        
        // Deliveries sin asignar (no tienen registro en delivery_assignments)
        $unassignedToday = Delivery::whereDate('delivery_date', $today)
            ->whereDoesntHave('deliveryAssignments')
            ->count();
        
        // Deliveries pendientes (asignadas pero no agarradas)
        $pendingToday = Delivery::whereDate('delivery_date', $today)
            ->whereHas('deliveryAssignments', function($query) {
                $query->where('driver_status', 'pendiente');
            })->count();
        
        // Deliveries agarradas (conductor las tomó pero no inició ruta)
        $grabbedToday = Delivery::whereDate('delivery_date', $today)
            ->whereHas('deliveryAssignments', function($query) {
                $query->where('driver_status', 'agarrado');
            })->count();
        
        // Deliveries en ruta (conductor en camino)
        $inRouteToday = Delivery::whereDate('delivery_date', $today)
            ->whereHas('deliveryAssignments', function($query) {
                $query->where('driver_status', 'en_ruta');
            })->count();
        
        // Deliveries completadas
        $completedToday = Delivery::whereDate('delivery_date', $today)
            ->whereHas('deliveryAssignments', function($query) {
                $query->where('driver_status', 'completado');
            })->count();

        // Calculate completion rate
        $completionRate = $totalToday > 0 ? round(($completedToday / $totalToday) * 100) : 0;

        return [
            Stat::make('Sin Asignar', $unassignedToday)
                ->description('Entregas pendientes de asignación')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color('gray'),

            Stat::make('Pendientes', $pendingToday)
                ->description('Esperando aceptación del conductor')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Aceptadas', $grabbedToday)
                ->description('Listas para iniciar ruta')
                ->descriptionIcon('heroicon-m-hand-raised')
                ->color('info'),

            Stat::make('En Ruta', $inRouteToday)
                ->description('En proceso')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),

            Stat::make('Completadas', $completedToday)
                ->description("{$completionRate}% completado")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }

    protected function getColumns(): int
    {
        return 5;
    }
}