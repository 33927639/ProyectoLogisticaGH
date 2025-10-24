<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vehicle extends Model
{
    protected $table = 'vehicles';
    protected $primaryKey = 'id_vehicle';
    
    protected $fillable = [
        'license_plate',
        'capacity',
        'available',
        'status',
        'total_kilometers',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'available' => 'boolean',
        'status' => 'boolean',
        'total_kilometers' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function deliveryAssignments(): HasMany
    {
        return $this->hasMany(DeliveryAssignment::class, 'vehicle_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'vehicle_id');
    }

    public function fuelLogs(): HasMany
    {
        return $this->hasMany(FuelLog::class, 'vehicle_id');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class, 'vehicle_id');
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'vehicle_id');
    }

    public function kilometers(): HasMany
    {
        return $this->hasMany(TblKilometer::class, 'id_vehicle', 'id_vehicle');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'vehicle_id');
    }

    public function vehicleTracking(): HasMany
    {
        return $this->hasMany(VehicleTracking::class, 'vehicle_id');
    }

    /**
     * Deliveries assigned to this vehicle
     */
    public function deliveries(): BelongsToMany
    {
        return $this->belongsToMany(Delivery::class, 'delivery_assignments', 'vehicle_id', 'delivery_id')
                    ->withPivot('driver_id', 'assignment_date', 'status')
                    ->withTimestamps();
    }

    /**
     * Add kilometers to the vehicle
     */
    public function addKilometers(float $kilometers, string $notes = '', $userId = null): TblKilometer
    {
        // Create kilometer log
        $kilometerLog = TblKilometer::create([
            'id_vehicle' => $this->id_vehicle,
            'kilometers' => $kilometers,
            'date' => now()->toDateString(),
            'notes' => $notes,
            'id_user' => $userId ?? (auth()->user()->id_user ?? null)
        ]);

        // Update vehicle total kilometers
        $this->increment('total_kilometers', $kilometers);

        return $kilometerLog;
    }

    /**
     * Check if vehicle needs maintenance (every 10,000 km)
     */
    public function needsMaintenance(): bool
    {
        return $this->total_kilometers >= 10000;
    }

    /**
     * Get kilometers until next maintenance
     */
    public function kilometersUntilMaintenance(): float
    {
        $nextMaintenance = ceil($this->total_kilometers / 10000) * 10000;
        return max(0, $nextMaintenance - $this->total_kilometers);
    }
}
