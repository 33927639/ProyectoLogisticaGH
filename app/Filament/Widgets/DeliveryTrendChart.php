<?php

namespace App\Filament\Widgets;

use App\Models\Delivery;
use App\Models\DeliveryStatus;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Gate;

class DeliveryTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Entregas de los Últimos 7 Días';
    protected static ?int $sort = 4;
    protected static ?string $pollingInterval = '15s'; // Más frecuente para mayor tiempo real

    protected function getData(): array
    {
        // Check authorization
        if (!Gate::allows('viewAny', Delivery::class)) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $deliveredStatus = DeliveryStatus::where('name_status', 'Entregado')->first();
        
        // Get last 7 days
        $labels = [];
        $completedData = [];
        $totalData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');

            // Total deliveries for the day
            $totalDeliveries = Delivery::whereDate('delivery_date', $date)->count();
            $totalData[] = $totalDeliveries;

            // Completed deliveries for the day
            $completedDeliveries = Delivery::whereDate('delivery_date', $date)
                ->where('status_id', $deliveredStatus?->id_status)
                ->count();
            $completedData[] = $completedDeliveries;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Programadas',
                    'data' => $totalData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Completadas',
                    'data' => $completedData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
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
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];
    }
}