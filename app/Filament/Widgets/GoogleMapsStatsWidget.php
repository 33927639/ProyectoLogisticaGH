<?php

namespace App\Filament\Widgets;

use App\Models\Route;
use App\Services\GoogleMapsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GoogleMapsStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null; // No auto-refresh para evitar límites de API
    
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        $totalRoutes = Route::count();
        $routesWithDistance = Route::whereNotNull('distance_km')->count();
        $totalDistance = Route::sum('distance_km');
        
        // Verificar si la columna estimated_duration existe
        $totalDuration = 0;
        try {
            $totalDuration = Route::sum('estimated_duration');
        } catch (\Exception $e) {
            // Si la columna no existe, calcular estimación básica: distancia * 1.5 minutos por km
            $totalDuration = $totalDistance * 1.5;
        }

        $apiConfigured = config('services.google_maps.api_key') ? 'Configurada' : 'No Configurada';
        $apiColor = config('services.google_maps.api_key') ? 'success' : 'danger';

        return [
            Stat::make('Google Maps API', $apiConfigured)
                ->description('Estado de la configuración')
                ->descriptionIcon(config('services.google_maps.api_key') ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($apiColor),

            Stat::make('Rutas Calculadas', "{$routesWithDistance} / {$totalRoutes}")
                ->description('Rutas con distancia calculada')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('info'),

            Stat::make('Distancia Total', number_format($totalDistance, 2) . ' km')
                ->description('Suma de todas las rutas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),

            Stat::make('Tiempo Estimado', number_format($totalDuration / 60, 1) . ' hrs')
                ->description('Tiempo estimado total')
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),
        ];
    }
}