<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use App\Models\Maintenance;
use App\Models\Delivery;
use App\Models\DeliveryStatus;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Gate;

class AlertsWidget extends Widget
{
    protected static string $view = 'filament.widgets.alerts-widget';
    protected static ?int $sort = 7;
    protected static ?string $pollingInterval = '60s';

    protected function getViewData(): array
    {
        $alerts = [];

        // Critical vehicle maintenance alerts
        if (Gate::allows('manage-fleet')) {
            $criticalMaintenance = Maintenance::where('status', 'Pendiente')
                ->where('maintenance_date', '<=', now()->addDays(3))
                ->with('vehicle')
                ->get();

            foreach ($criticalMaintenance as $maintenance) {
                $alerts[] = [
                    'type' => 'danger',
                    'icon' => 'heroicon-o-exclamation-triangle',
                    'title' => 'Mantenimiento Crítico',
                    'message' => "Vehículo {$maintenance->vehicle->license_plate} requiere mantenimiento urgente ({$maintenance->type})",
                    'action_url' => route('filament.admin.resources.maintenance.view', $maintenance),
                ];
            }
        }

        // Overdue deliveries
        if (Gate::allows('viewAny', Delivery::class)) {
            $pendingStatus = DeliveryStatus::where('name_status', 'Pendiente')->first();
            $overdueDeliveries = Delivery::where('status_id', $pendingStatus?->id_status)
                ->where('delivery_date', '<', today())
                ->count();

            if ($overdueDeliveries > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'icon' => 'heroicon-o-clock',
                    'title' => 'Entregas Atrasadas',
                    'message' => "{$overdueDeliveries} entregas pendientes de días anteriores",
                    'action_url' => route('filament.admin.resources.deliveries.index'),
                ];
            }
        }

        // Vehicles requiring maintenance (based on maintenance requests)
        if (Gate::allows('manage-fleet')) {
            $vehiclesNeedingMaintenance = \App\Models\MaintenanceRequest::where('status', 'Pendiente')
                ->distinct('vehicle_id')
                ->count('vehicle_id');

            if ($vehiclesNeedingMaintenance > 0) {
                $alerts[] = [
                    'type' => 'info',
                    'icon' => 'heroicon-o-clipboard-document-check',
                    'title' => 'Solicitudes de Mantenimiento',
                    'message' => "{$vehiclesNeedingMaintenance} vehículos tienen solicitudes de mantenimiento pendientes",
                    'action_url' => route('filament.admin.resources.vehicles.index'),
                ];
            }
        }

        // Low profit margin alert (financial)
        if (Gate::allows('access-financial-reports')) {
            $thisMonth = now()->startOfMonth();
            $monthIncome = \App\Models\Income::where('created_at', '>=', $thisMonth)->sum('amount');
            $monthExpenses = \App\Models\Expense::where('expense_date', '>=', $thisMonth)->sum('amount');
            
            if ($monthIncome > 0) {
                $profitMargin = (($monthIncome - $monthExpenses) / $monthIncome) * 100;
                
                if ($profitMargin < 10) {
                    $alerts[] = [
                        'type' => 'warning',
                        'icon' => 'heroicon-o-chart-bar',
                        'title' => 'Margen de Utilidad Bajo',
                        'message' => "Margen actual: " . round($profitMargin, 1) . "% - Revisar costos operativos",
                        'action_url' => route('filament.admin.resources.expenses.index'),
                    ];
                }
            }
        }

        return [
            'alerts' => collect($alerts)->take(5), // Show max 5 alerts
        ];
    }
}