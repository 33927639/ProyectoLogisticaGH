<?php

namespace App\Filament\Resources\IncomeResource\Widgets;

use App\Models\Income;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IncomeStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Ingresos', 'Q' . number_format(Income::where('status', true)->sum('amount'), 2))
                ->description('Total de ingresos activos')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
            
            Stat::make('Ingresos Este Mes', 'Q' . number_format(
                Income::where('status', true)
                    ->whereMonth('income_date', now()->month)
                    ->whereYear('income_date', now()->year)
                    ->sum('amount'), 2
            ))
                ->description('Ingresos del mes actual')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
            
            Stat::make('Ingresos Hoy', 'Q' . number_format(
                Income::where('status', true)
                    ->whereDate('income_date', now())
                    ->sum('amount'), 2
            ))
                ->description('Ingresos de hoy')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
        ];
    }
}
