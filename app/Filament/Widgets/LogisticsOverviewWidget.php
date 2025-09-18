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
        // Camiones stats
        $totalCamiones = Camion::count();
        $camionesActivos = Camion::where('estado', Camion::ESTADO_ACTIVO)->count();
        $camionesEnTaller = Camion::where('estado', Camion::ESTADO_EN_TALLER)->count();
        $camionesNecesitanMantenimiento = Camion::whereRaw('kilometraje_actual >= (
            SELECT COALESCE(MAX(kilometraje_actual), 0) + intervalo_mantenimiento_km
            FROM mantemientos 
            WHERE camiones.id = mantemientos.camion_id 
            AND estado = "Completado"
        )')->count();

        // Pilotos stats
        $totalPilotos = Piloto::count();
        $pilotosDisponibles = Piloto::where('estado', Piloto::ESTADO_ACTIVO)
            ->whereDoesntHave('viajes', function ($query) {
                $query->where('estado', 'En Curso');
            })->count();

        // Viajes stats
        $viajesEnCurso = Viaje::where('estado', Viaje::ESTADO_EN_CURSO)->count();
        $viajesHoy = Viaje::whereDate('fecha_inicio', Carbon::today())->count();
        $viajesRetrasados = Viaje::where('estado', Viaje::ESTADO_EN_CURSO)
            ->whereRaw('NOW() > DATE_ADD(fecha_inicio, INTERVAL (SELECT tiempo_estimado_horas FROM rutas WHERE id = viajes.ruta_id) HOUR)')
            ->count();

        // Mantenimientos stats
        $mantenimientosVencidos = Mantenimiento::where('estado', Mantenimiento::ESTADO_PROGRAMADO)
            ->where('fecha_programada', '<', Carbon::today())
            ->count();
        
        $mantenimientosProximos = Mantenimiento::where('estado', Mantenimiento::ESTADO_PROGRAMADO)
            ->whereBetween('fecha_programada', [
                Carbon::today(),
                Carbon::today()->addDays(7)
            ])
            ->count();

        return [
            // Flota
            Stat::make('Camiones Activos', $camionesActivos . ' / ' . $totalCamiones)
                ->description($camionesEnTaller ? "{$camionesEnTaller} en taller" : 'Todos operativos')
                ->descriptionIcon($camionesEnTaller > 0 ? 'heroicon-m-wrench-screwdriver' : 'heroicon-m-check-circle')
                ->color($camionesEnTaller > ($totalCamiones * 0.2) ? 'warning' : 'success')
                ->chart([7, 8, 6, 9, 10, 8, 11])
                ->chartColor('success'),

            Stat::make('Pilotos Disponibles', $pilotosDisponibles . ' / ' . $totalPilotos)
                ->description($pilotosDisponibles > 5 ? 'Suficiente personal' : 'Personal limitado')
                ->descriptionIcon($pilotosDisponibles > 5 ? 'heroicon-m-user-group' : 'heroicon-m-exclamation-triangle')
                ->color($pilotosDisponibles > 5 ? 'success' : 'warning'),

            // Operaciones
            Stat::make('Viajes en Curso', $viajesEnCurso)
                ->description($viajesRetrasados ? "{$viajesRetrasados} retrasados" : 'Todos en tiempo')
                ->descriptionIcon($viajesRetrasados > 0 ? 'heroicon-m-clock' : 'heroicon-m-truck')
                ->color($viajesRetrasados > 0 ? 'warning' : 'info')
                ->chart([3, 5, 8, 6, 9, 7, 4])
                ->chartColor('info'),

            Stat::make('Viajes Hoy', $viajesHoy)
                ->description('Programados para hoy')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            // Mantenimiento
            Stat::make('Mant. Vencidos', $mantenimientosVencidos)
                ->description($mantenimientosVencidos > 0 ? 'Requieren atención' : 'Al día')
                ->descriptionIcon($mantenimientosVencidos > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($mantenimientosVencidos > 0 ? 'danger' : 'success'),

            Stat::make('Próximos Mant.', $mantenimientosProximos)
                ->description('En los próximos 7 días')
                ->descriptionIcon('heroicon-m-calendar')
                ->color($mantenimientosProximos > 10 ? 'warning' : 'info'),

            // Alerta crítica
            Stat::make('Camiones Críticos', $camionesNecesitanMantenimiento)
                ->description('Necesitan mantenimiento urgente')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($camionesNecesitanMantenimiento > 0 ? 'danger' : 'success')
                ->chart($camionesNecesitanMantenimiento > 0 ? [10, 12, 15, 18, 20] : [5, 3, 2, 1, 0])
                ->chartColor($camionesNecesitanMantenimiento > 0 ? 'danger' : 'success'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }

    public function getDisplayName(): string
    {
        return 'Resumen de Logística';
    }
}