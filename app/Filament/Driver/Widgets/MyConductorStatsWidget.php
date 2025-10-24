<?php

namespace App\Filament\Driver\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\DeliveryAssignment;
use Illuminate\Support\Facades\Auth;

class MyConductorStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    // Actualizar cada 5 segundos
    protected static ?string $pollingInterval = '5s';
    
    protected function getStats(): array
    {
        $currentDriver = $this->getCurrentDriver();
        
        if (!$currentDriver) {
            return [
                Stat::make('Sin acceso', 'No se pudo identificar el conductor')
                    ->color('danger'),
            ];
        }

        // Obtener contadores en tiempo real
        $pending = DeliveryAssignment::where('driver_id', $currentDriver->id_driver)
            ->where('driver_status', 'pendiente')
            ->count();

        $grabbed = DeliveryAssignment::where('driver_id', $currentDriver->id_driver)
            ->where('driver_status', 'agarrado')
            ->count();

        $inRoute = DeliveryAssignment::where('driver_id', $currentDriver->id_driver)
            ->where('driver_status', 'en_ruta')
            ->count();

        $completedToday = DeliveryAssignment::where('driver_id', $currentDriver->id_driver)
            ->where('driver_status', 'completado')
            ->whereHas('delivery', function ($query) {
                $query->whereDate('delivery_date', today());
            })
            ->count();

        $totalCompleted = DeliveryAssignment::where('driver_id', $currentDriver->id_driver)
            ->where('driver_status', 'completado')
            ->count();

        return [
            Stat::make('Pendientes', $pending)
                ->description('Entregas por agarrar 👋')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Agarradas', $grabbed)
                ->description('Listas para iniciar ✋')
                ->descriptionIcon('heroicon-m-hand-raised')
                ->color('info')
                ->chart([15, 4, 10, 2, 12, 4, 12]),

            Stat::make('En Ruta', $inRoute)
                ->description('Entregas en progreso 🚛')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary')
                ->chart([3, 2, 12, 7, 13, 5, 17]),

            Stat::make('Completadas Hoy', $completedToday)
                ->description('Entregas finalizadas ✅')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([2, 10, 15, 22, 24, 12, 16]),

            Stat::make('Total Completadas', $totalCompleted)
                ->description('Entregas históricas 🏆')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),
        ];
    }

    private function getCurrentDriver()
    {
        try {
            $user = Auth::guard('driver')->user();
            if (!$user) {
                return null;
            }

            // Buscar conductor por nombre completo
            $fullName = trim($user->first_name . ' ' . $user->last_name);
            $driver = Driver::where('name', 'LIKE', '%' . $fullName . '%')->first();
            
            // Si no se encuentra, usar el primer conductor disponible para testing
            if (!$driver) {
                $driver = Driver::first();
                if (!$driver) {
                    // Crear un driver temporal para testing
                    $driver = new Driver();
                    $driver->id_driver = $user->id;
                    $driver->name = $fullName;
                }
            }
            
            return $driver;
        } catch (\Exception $e) {
            \Log::error('Error getting current driver in widget: ' . $e->getMessage());
            return null;
        }
    }
}