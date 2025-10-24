<?php

namespace App\Filament\Widgets;

use App\Models\TblVehicle;
use App\Models\TblKilometer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class VehicleKilometerWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();
        
        // Verificar si el usuario tiene roles permitidos
        if (!$user || !$user->roles()->exists()) {
            return false;
        }
        
        $allowedRoles = ['Administrador', 'Supervisor', 'Operador'];
        return $user->hasRole($allowedRoles);
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        
        // Verificación adicional de seguridad
        if (!$user || !$user->hasRole(['Administrador', 'Supervisor', 'Operador'])) {
            return [];
        }

        $totalVehicles = TblVehicle::count();
        $activeVehicles = TblVehicle::where('status_vehicle', 'Activo')->count();
        
        // Obtener el kilometraje total de todos los vehículos
        $totalKilometers = TblKilometer::sum('current_kilometer');
        
        // Obtener el último registro de kilometraje
        $lastKilometerRecord = TblKilometer::latest('date_kilometer')->first();
        $lastUpdate = $lastKilometerRecord ? $lastKilometerRecord->date_kilometer : 'Sin registros';

        return [
            Stat::make('Total de Vehículos', $totalVehicles)
                ->description('Vehículos registrados')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
                
            Stat::make('Vehículos Activos', $activeVehicles)
                ->description('En operación')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Kilometraje Total', number_format($totalKilometers) . ' km')
                ->description('Acumulado de todos los vehículos')
                ->descriptionIcon('heroicon-m-map')
                ->color('warning'),
                
            Stat::make('Última Actualización', $lastUpdate)
                ->description('Último registro de kilometraje')
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray'),
        ];
    }
}