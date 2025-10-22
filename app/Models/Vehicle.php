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
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'available' => 'boolean',
        'status' => 'boolean',
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
        return $this->hasMany(Kilometer::class, 'vehicle_id');
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
}
