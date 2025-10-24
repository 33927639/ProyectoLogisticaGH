<?php

namespace App\Filament\Widgets;

use App\Models\Income;
use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Gate;

class FinancialStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '60s'; // ESTADÍSTICO: Datos financieros
    protected static bool $isLazy = false;
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        // Check authorization for financial data
        if (!Gate::allows('access-financial-reports')) {
            return [];
        }

        $today = today();
        $thisMonth = now()->startOfMonth();

        // Today's financial data
        $todayIncome = Income::whereDate('created_at', $today)->sum('amount');
        $todayExpenses = Expense::whereDate('expense_date', $today)->sum('amount');
        $todayProfit = $todayIncome - $todayExpenses;

        // This month's financial data
        $monthIncome = Income::where('created_at', '>=', $thisMonth)->sum('amount');
        $monthExpenses = Expense::where('expense_date', '>=', $thisMonth)->sum('amount');
        $monthProfit = $monthIncome - $monthExpenses;

        // Calculate profit margin
        $profitMargin = $monthIncome > 0 ? round(($monthProfit / $monthIncome) * 100, 1) : 0;

        return [
            Stat::make('Ingresos del Mes', 'Q' . number_format($monthIncome, 2))
                ->description('Q' . number_format($todayIncome, 2) . ' hoy')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Gastos del Mes', 'Q' . number_format($monthExpenses, 2))
                ->description('Q' . number_format($todayExpenses, 2) . ' hoy')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Utilidad del Mes', 'Q' . number_format($monthProfit, 2))
                ->description('Q' . number_format($todayProfit, 2) . ' hoy')
                ->descriptionIcon($monthProfit >= 0 ? 'heroicon-m-chart-bar' : 'heroicon-m-exclamation-triangle')
                ->color($monthProfit >= 0 ? 'success' : 'danger'),

            Stat::make('Margen de Utilidad', $profitMargin . '%')
                ->description($profitMargin >= 20 ? 'Excelente' : ($profitMargin >= 10 ? 'Bueno' : 'Mejorar'))
                ->descriptionIcon('heroicon-m-calculator')
                ->color($profitMargin >= 20 ? 'success' : ($profitMargin >= 10 ? 'warning' : 'danger')),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}