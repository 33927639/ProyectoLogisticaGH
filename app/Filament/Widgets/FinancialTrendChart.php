<?php

namespace App\Filament\Widgets;

use App\Models\Income;
use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Gate;

class FinancialTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Tendencia Financiera (Últimos 6 Meses)';
    protected static ?int $sort = 6;
    protected static ?string $pollingInterval = '10m';

    protected function getData(): array
    {
        // Check authorization for financial data
        if (!Gate::allows('access-financial-reports')) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $labels = [];
        $incomeData = [];
        $expenseData = [];

        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M Y');

            // Income for the month
            $monthIncome = Income::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            $incomeData[] = $monthIncome;

            // Expenses for the month
            $monthExpenses = Expense::whereYear('expense_date', $month->year)
                ->whereMonth('expense_date', $month->month)
                ->sum('amount');
            $expenseData[] = $monthExpenses;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ingresos',
                    'data' => $incomeData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
                [
                    'label' => 'Gastos',
                    'data' => $expenseData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "Q" + value.toLocaleString(); }',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return context.dataset.label + ": Q" + context.parsed.y.toLocaleString(); }',
                    ],
                ],
            ],
        ];
    }
}