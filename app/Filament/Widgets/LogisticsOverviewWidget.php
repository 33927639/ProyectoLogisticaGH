<?php

namespace App\Filament\Widgets;

use App\Models\Camion;
use App\Models\Piloto;
use App\Models\Viaje;
use App\Models\Mantenimiento;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class LogisticsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Datos de camiones
        $totalCamiones = Camion::count();
        $camionesActivos = Camion::where('estado', Camion::ESTADO_ACTIVO)->count();
        $camionesEnTaller = Camion::where('estado', Camion::ESTADO_EN_TALLER)->count();

        // Datos de pilotos
        $totalPilotos = Piloto::count();
        $pilotosDisponibles = Piloto::where('estado', Piloto::ESTADO_ACTIVO)
            ->whereDoesntHave('viajes', fn($query) => $query->where('estado', 'En Curso'))
            ->count();

        // Datos de viajes
        $viajesHoy = Viaje::whereDate('fecha_inicio', Carbon::today())->count();
        $viajesEnCurso = Viaje::where('estado', Viaje::ESTADO_EN_CURSO)->count();

        // Datos de mantenimiento
        $mantenimientosVencidos = Mantenimiento::where('estado', Mantenimiento::ESTADO_PROGRAMADO)
            ->where('fecha_programada', '<', Carbon::today())
            ->count();
        $mantenimientosProximos = Mantenimiento::where('estado', Mantenimiento::ESTADO_PROGRAMADO)
            ->whereBetween('fecha_programada', [Carbon::today(), Carbon::today()->addDays(7)])
            ->count();

        return [
            Stat::make('Camiones Activos', $camionesActivos . ' / ' . $totalCamiones)
                ->description($camionesEnTaller > 0 ? "{$camionesEnTaller} en taller" : 'Todos operativos')
                ->descriptionIcon('heroicon-s-truck')
                ->color('success')
                ->extraAttributes(['style' => 'background-color: #d4edda; border-radius: 12px; padding: 20px;']),

            Stat::make('Pilotos Disponibles', $pilotosDisponibles . ' / ' . $totalPilotos)
                ->description($pilotosDisponibles > 5 ? 'Suficiente personal' : 'Personal limitado')
                ->descriptionIcon('heroicon-s-user-group')
                ->color('primary')
                ->extraAttributes(['style' => 'background-color: #cce5ff; border-radius: 12px; padding: 20px;']),

            Stat::make('Viajes en Curso', $viajesEnCurso)
                ->description('Viajes activos')
                ->descriptionIcon('heroicon-s-clock')
                ->color('warning')
                ->extraAttributes(['style' => 'background-color: #fff3cd; border-radius: 12px; padding: 20px;']),

            Stat::make('Viajes Hoy', $viajesHoy)
                ->description('Programados para hoy')
                ->descriptionIcon('heroicon-s-calendar')
                ->color('info')
                ->extraAttributes(['style' => 'background-color: #e2e3ff; border-radius: 12px; padding: 20px;']),

            Stat::make('Mantenimientos Vencidos', $mantenimientosVencidos)
                ->description($mantenimientosVencidos > 0 ? 'Requieren atención' : 'Al día')
                ->descriptionIcon('heroicon-s-exclamation-triangle')
                ->color('danger')
                ->extraAttributes(['style' => 'background-color: #f8d7da; border-radius: 12px; padding: 20px;']),

            Stat::make('Próximos Mantenimientos', $mantenimientosProximos)
                ->description('En los próximos 7 días')
                ->descriptionIcon('heroicon-s-calendar')
                ->color('warning')
                ->extraAttributes(['style' => 'background-color: #fff3cd; border-radius: 12px; padding: 20px;']),
        ];
    }

    protected function getColumns(): int
    {
        return 2; // 2 tarjetas por fila
    }

    public function getDisplayName(): string
    {
        return 'Resumen de Logística';
    }
}
