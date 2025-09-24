<?php

namespace App\Filament\Widgets;

use App\Models\TblProduct;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalStock extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total en Stock', TblProduct::sum('stock'))
                ->description('Cantidad total de unidades en inventario')
                ->icon('heroicon-o-cube')
                ->color('success'),
        ];
    }
}
