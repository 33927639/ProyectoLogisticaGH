<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VentasMensuales extends BaseWidget
{
    protected function getStats(): array
    {
        $totalVentas = Venta::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('monto');

        return [
            Stat::make('Ventas del Mes', number_format($totalVentas, 2))
                ->description('Total de ventas Q del mes actual')
                ->color('success')
                ->icon('heroicon-o-currency-dollar'),
        ];
    }
}
