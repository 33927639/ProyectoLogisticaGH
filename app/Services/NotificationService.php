<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Maintenance;
use App\Models\Delivery;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create a new notification
     */
    public static function create(array $data): Notification
    {
        return Notification::create([
            'user_id' => $data['user_id'],
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'maintenance_id' => $data['maintenance_id'] ?? null,
            'delivery_id' => $data['delivery_id'] ?? null,
            'type' => $data['type'],
            'channel' => $data['channel'] ?? Notification::CHANNEL_IN_APP,
            'message' => $data['message'],
            'trigger_km' => $data['trigger_km'] ?? null,
            'sent' => false,
            'created_at' => now(),
        ]);
    }

    /**
     * Create maintenance notification
     */
    public static function createMaintenanceNotification(Maintenance $maintenance, string $message): void
    {
        // Notify supervisors and admins
        $users = User::whereHas('roles', function($query) {
            $query->whereIn('name_role', ['Super Administrador', 'Administrador', 'Supervisor']);
        })->get();

        foreach ($users as $user) {
            self::create([
                'user_id' => $user->id_user,
                'maintenance_id' => $maintenance->id_maintenance,
                'vehicle_id' => $maintenance->vehicle_id,
                'type' => Notification::TYPE_MAINTENANCE,
                'message' => $message,
            ]);
        }
    }

    /**
     * Create delivery notification
     */
    public static function createDeliveryNotification(Delivery $delivery, string $message, ?int $userId = null): void
    {
        if ($userId) {
            // Specific user notification
            self::create([
                'user_id' => $userId,
                'delivery_id' => $delivery->id_delivery,
                'type' => Notification::TYPE_DELIVERY,
                'message' => $message,
            ]);
        } else {
            // Notify all operational users
            $users = User::whereHas('roles', function($query) {
                $query->whereIn('name_role', ['Super Administrador', 'Administrador', 'Supervisor', 'Operador']);
            })->get();

            foreach ($users as $user) {
                self::create([
                    'user_id' => $user->id_user,
                    'delivery_id' => $delivery->id_delivery,
                    'type' => Notification::TYPE_DELIVERY,
                    'message' => $message,
                ]);
            }
        }
    }

    /**
     * Create vehicle notification
     */
    public static function createVehicleNotification(Vehicle $vehicle, string $message): void
    {
        // Notify fleet managers
        $users = User::whereHas('roles', function($query) {
            $query->whereIn('name_role', ['Super Administrador', 'Administrador', 'Supervisor']);
        })->get();

        foreach ($users as $user) {
            self::create([
                'user_id' => $user->id_user,
                'vehicle_id' => $vehicle->id_vehicle,
                'type' => Notification::TYPE_VEHICLE,
                'message' => $message,
            ]);
        }
    }

    /**
     * Create financial notification
     */
    public static function createFinancialNotification(string $message): void
    {
        // Notify financial managers
        $users = User::whereHas('roles', function($query) {
            $query->whereIn('name_role', ['Super Administrador', 'Administrador']);
        })->get();

        foreach ($users as $user) {
            self::create([
                'user_id' => $user->id_user,
                'type' => Notification::TYPE_FINANCIAL,
                'message' => $message,
            ]);
        }
    }

    /**
     * Create system notification
     */
    public static function createSystemNotification(string $message, ?int $userId = null): void
    {
        if ($userId) {
            self::create([
                'user_id' => $userId,
                'type' => Notification::TYPE_SYSTEM,
                'message' => $message,
            ]);
        } else {
            // Notify all users
            $users = User::where('status', true)->get();
            foreach ($users as $user) {
                self::create([
                    'user_id' => $user->id_user,
                    'type' => Notification::TYPE_SYSTEM,
                    'message' => $message,
                ]);
            }
        }
    }

    /**
     * Get unread notifications for user
     */
    public static function getUnreadForUser(int $userId): Collection
    {
        return Notification::forUser($userId)
            ->unread()
            ->inApp()
            ->with(['vehicle', 'maintenance', 'delivery'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get unread count for user
     */
    public static function getUnreadCountForUser(int $userId): int
    {
        return Notification::forUser($userId)
            ->unread()
            ->inApp()
            ->count();
    }

    /**
     * Mark all notifications as read for user
     */
    public static function markAllAsReadForUser(int $userId): void
    {
        Notification::forUser($userId)
            ->unread()
            ->inApp()
            ->update([
                'sent' => true,
                'sent_at' => now(),
            ]);
    }

    /**
     * Generate automatic notifications based on system state
     */
    public static function generateAutomaticNotifications(): void
    {
        // Check for overdue deliveries
        $overdueDeliveries = Delivery::whereHas('deliveryStatus', function($query) {
                $query->where('name_status', 'Pendiente');
            })
            ->where('delivery_date', '<', today())
            ->get();

        foreach ($overdueDeliveries as $delivery) {
            self::createDeliveryNotification(
                $delivery,
                "Entrega #{$delivery->id_delivery} está atrasada desde {$delivery->delivery_date->format('d/m/Y')}"
            );
        }

        // Check for pending maintenance
        $pendingMaintenance = Maintenance::where('status', 'Pendiente')
            ->where('maintenance_date', '<=', now()->addDays(3))
            ->get();

        foreach ($pendingMaintenance as $maintenance) {
            self::createMaintenanceNotification(
                $maintenance,
                "Mantenimiento del vehículo {$maintenance->vehicle->license_plate} está programado para {$maintenance->maintenance_date->format('d/m/Y')}"
            );
        }

        // Check for low profit margin
        $thisMonth = now()->startOfMonth();
        $monthIncome = \App\Models\Income::where('created_at', '>=', $thisMonth)->sum('amount');
        $monthExpenses = \App\Models\Expense::where('expense_date', '>=', $thisMonth)->sum('amount');
        
        if ($monthIncome > 0) {
            $profitMargin = (($monthIncome - $monthExpenses) / $monthIncome) * 100;
            
            if ($profitMargin < 10) {
                self::createFinancialNotification(
                    "Margen de utilidad bajo este mes: " . round($profitMargin, 1) . "%. Revisar gastos operativos."
                );
            }
        }
    }
}