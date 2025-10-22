<?php

namespace App\Filament\Driver\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\TblDelivery;
use App\Models\TblDriver;
use Illuminate\Support\Facades\Auth;

class MyConductorStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $currentDriver = $this->getCurrentDriver();
        
        if (!$currentDriver) {
            return [
                Stat::make('Sin acceso', 'No se pudo identificar el conductor')
                    ->color('danger'),
            ];
        }

        // Obtener estadísticas de entregas asignadas
        $totalAssigned = TblDelivery::whereHas('assignments', function ($query) use ($currentDriver) {
            $query->where('driver_id', $currentDriver->driver_id);
        })->count();

        $completedToday = TblDelivery::whereHas('assignments', function ($query) use ($currentDriver) {
            $query->where('driver_id', $currentDriver->driver_id)
                  ->where('driver_status', 'completado');
        })->whereDate('delivery_date', today())->count();

        $inRoute = TblDelivery::whereHas('assignments', function ($query) use ($currentDriver) {
            $query->where('driver_id', $currentDriver->driver_id)
                  ->where('driver_status', 'en_ruta');
        })->count();

        $pending = TblDelivery::whereHas('assignments', function ($query) use ($currentDriver) {
            $query->where('driver_id', $currentDriver->driver_id)
                  ->whereIn('driver_status', ['pendiente', 'agarrado']);
        })->count();

        return [
            Stat::make('Total Asignadas', $totalAssigned)
                ->description('Entregas asignadas a mi nombre')
                ->color('primary'),
                
            Stat::make('Completadas Hoy', $completedToday)
                ->description('Entregas completadas hoy')
                ->color('success'),
                
            Stat::make('En Ruta', $inRoute)
                ->description('Entregas actualmente en ruta')
                ->color('warning'),
                
            Stat::make('Pendientes', $pending)
                ->description('Entregas pendientes por entregar')
                ->color('info'),
        ];
    }

    private function getCurrentDriver()
    {
        try {
            $user = Auth::guard('driver')->user();
            if (!$user) {
                return null;
            }

            return TblDriver::where('first_name', $user->first_name)
                           ->where('last_name', $user->last_name)
                           ->first();
        } catch (\Exception $e) {
            return null;
        }
    }
}