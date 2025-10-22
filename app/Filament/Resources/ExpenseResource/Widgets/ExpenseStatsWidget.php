<?php

namespace App\Filament\Resources\ExpenseResource\Widgets;

use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpenseStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Gastos', 'Q' . number_format(Expense::where('status', true)->sum('amount'), 2))
                ->description('Total de gastos activos')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
            
            Stat::make('Gastos Este Mes', 'Q' . number_format(
                Expense::where('status', true)
                    ->whereMonth('expense_date', now()->month)
                    ->whereYear('expense_date', now()->year)
                    ->sum('amount'), 2
            ))
                ->description('Gastos del mes actual')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('danger'),
            
            Stat::make('Gastos Hoy', 'Q' . number_format(
                Expense::where('status', true)
                    ->whereDate('expense_date', now())
                    ->sum('amount'), 2
            ))
                ->description('Gastos de hoy')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
        ];
    }
}
