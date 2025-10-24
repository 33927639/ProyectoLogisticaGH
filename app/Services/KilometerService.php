<?php

namespace App\Services;

use App\Models\TblKilometer;
use App\Models\Vehicle;

class KilometerService
{
    /**
     * Get total kilometers for a vehicle
     */
    public static function getTotalKilometers(int $vehicleId): float
    {
        return TblKilometer::where('id_vehicle', $vehicleId)
            ->sum('kilometers') ?? 0;
    }

    /**
     * Add kilometers to a vehicle
     */
    public static function addKilometers(int $vehicleId, float $kilometers, string $notes = null, int $userId = null): bool
    {
        try {
            TblKilometer::create([
                'id_vehicle' => $vehicleId,
                'kilometers' => $kilometers,
                'date' => now()->toDateString(),
                'notes' => $notes,
                'id_user' => $userId ?? auth()->id(),
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get last kilometer reading for a vehicle
     */
    public static function getLastReading(int $vehicleId): ?TblKilometer
    {
        return TblKilometer::where('id_vehicle', $vehicleId)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get kilometers for a specific period
     */
    public static function getKilometersForPeriod(int $vehicleId, string $startDate, string $endDate): float
    {
        return TblKilometer::where('id_vehicle', $vehicleId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('kilometers') ?? 0;
    }

    /**
     * Get average kilometers per day for a vehicle
     */
    public static function getAverageKilometersPerDay(int $vehicleId, int $days = 30): float
    {
        $startDate = now()->subDays($days)->toDateString();
        $endDate = now()->toDateString();
        
        $totalKm = static::getKilometersForPeriod($vehicleId, $startDate, $endDate);
        
        return $days > 0 ? round($totalKm / $days, 2) : 0;
    }

    /**
     * Get vehicle efficiency (km per liter)
     */
    public static function getVehicleEfficiency(int $vehicleId): float
    {
        // This would require fuel consumption data
        // For now, return a default value
        return 0;
    }

    /**
     * Calculate maintenance due based on kilometers
     */
    public static function isMaintenanceDue(int $vehicleId, int $maintenanceInterval = 10000): bool
    {
        $totalKm = static::getTotalKilometers($vehicleId);
        $lastMaintenance = static::getLastMaintenanceKilometers($vehicleId);
        
        return ($totalKm - $lastMaintenance) >= $maintenanceInterval;
    }

    /**
     * Get kilometers at last maintenance
     */
    private static function getLastMaintenanceKilometers(int $vehicleId): float
    {
        // This would require maintenance records
        // For now, return 0
        return 0;
    }
}
