<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    protected $table = 'maintenances';
    protected $primaryKey = 'id_maintenance';
    
    protected $fillable = [
        'vehicle_id',
        'request_id',
        'type',
        'maintenance_date',
        'approved',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'approved' => 'boolean',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class, 'request_id');
    }
}
