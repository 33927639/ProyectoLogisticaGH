<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAssignment extends Model
{
    protected $table = 'delivery_assignments';
    
    // Usar clave primaria compuesta pero generar una clave única para Filament
    protected $primaryKey = ['delivery_id', 'vehicle_id', 'driver_id'];
    public $incrementing = false;
    
    // Usar timestamps automáticos
    public $timestamps = true;
    
    protected $fillable = [
        'delivery_id',
        'vehicle_id',
        'driver_id',
        'assignment_date',
        'assigned_at',
        'driver_status',
        'notes',
    ];

    protected $attributes = [
        'driver_status' => 'pendiente',
        'assignment_date' => null, // Se asignará automáticamente
        'assigned_at' => null, // Se asignará automáticamente
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->assignment_date)) {
                $model->assignment_date = now()->format('Y-m-d');
            }
            if (empty($model->assigned_at)) {
                $model->assigned_at = now();
            }
        });
    }

    protected $casts = [
        'assignment_date' => 'date',
        'assigned_at' => 'datetime',
        'delivery_id' => 'integer',
        'vehicle_id' => 'integer',
        'driver_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the value of the model's primary key.
     * Para Filament, generar una clave única combinando los IDs
     */
    public function getKey()
    {
        return $this->delivery_id . '-' . $this->vehicle_id . '-' . $this->driver_id;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKey()
    {
        return $this->getKey();
    }

    /**
     * Relationship with delivery
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class, 'delivery_id', 'id_delivery');
    }

    /**
     * Relationship with vehicle
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id_vehicle');
    }

    /**
     * Relationship with driver
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id_driver');
    }
}
