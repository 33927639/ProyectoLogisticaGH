<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    protected $table = 'routes';
    protected $primaryKey = 'id_route';
    
    protected $fillable = [
        'origin_id',
        'destination_id',
        'distance_km',
        'estimated_duration',
        'total_distance',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'estimated_duration' => 'integer',
        'total_distance' => 'decimal:2',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Municipality::class, 'origin_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Municipality::class, 'destination_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'route_id');
    }

    public function getRouteNameAttribute(): string
    {
        return "{$this->origin->name_municipality} → {$this->destination->name_municipality}";
    }

    /**
     * Find existing route between two municipalities
     */
    public static function findExistingRoute($originId, $destinationId)
    {
        return static::where('origin_id', $originId)
                     ->where('destination_id', $destinationId)
                     ->first();
    }
}
