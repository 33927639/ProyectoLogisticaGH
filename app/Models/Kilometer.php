<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kilometer extends Model
{
    protected $table = 'kilometers';
    protected $primaryKey = 'id_kilometer';
    
    protected $fillable = [
        'delivery_id',
        'vehicle_id',
        'alert_id',
        'kilometers_traveled',
        'record_date',
        'status'
    ];

    protected $casts = [
        'kilometers_traveled' => 'decimal:2',
        'record_date' => 'date',
        'status' => 'boolean'
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id_vehicle');
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class, 'delivery_id', 'id_delivery');
    }
}
