<?php

namespace App\Models;

use App\Traits\HasCompositeKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAssignment extends Model
{
    use HasCompositeKey;
    
    protected $table = 'delivery_assignments';
    
    // Clave primaria compuesta
    protected $primaryKey = ['delivery_id', 'vehicle_id', 'driver_id'];
    
    // No hay clave primaria única, será manejado por Filament de manera especial
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
