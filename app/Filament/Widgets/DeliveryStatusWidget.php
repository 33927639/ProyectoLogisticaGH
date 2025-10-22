<?php

namespace App\Filament\Widgets;

use App\Models\Delivery;
use App\Models\DeliveryAssignment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DeliveryStatusWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Entregas pendientes de asignación
        $pendingAssignment = Delivery::whereDoesntHave('deliveryAssignments')->count();

        // Entregas pendientes de aceptar por conductores
        $pendingAcceptance = DeliveryAssignment::where('status', 'pendiente')->count();

        // Entregas aceptadas (listas para iniciar)
        $accepted = DeliveryAssignment::where('status', 'aceptado')->count();

        // Entregas en ruta
        $inRoute = DeliveryAssignment::where('status', 'en_ruta')->count();

        // Entregas completadas hoy
        $completedToday = DeliveryAssignment::where('status', 'completado')
            ->whereDate('updated_at', today())
            ->count();

        // Entregas rechazadas
        $rejected = DeliveryAssignment::where('status', 'rechazado')->count();

        return [
            Stat::make('Sin Asignar', $pendingAssignment)
                ->description('Entregas pendientes de asignación')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('gray'),
                
            Stat::make('Pendientes', $pendingAcceptance)
                ->description('Esperando aceptación del conductor')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Aceptadas', $accepted)
                ->description('Listas para iniciar ruta')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),
                
            Stat::make('En Ruta', $inRoute)
                ->description('Entregas en progreso')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
                
            Stat::make('Completadas Hoy', $completedToday)
                ->description('Entregas finalizadas hoy')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
                
            Stat::make('Rechazadas', $rejected)
                ->description('Entregas rechazadas por conductores')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}