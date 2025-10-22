<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\GoogleMapsService;

class Route extends Model
{
    protected $table = 'routes';
    protected $primaryKey = 'id_route';
    
    protected $fillable = [
        'origin_id',
        'destination_id',
        'distance_km',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
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
     * Buscar ruta existente entre dos municipios (en cualquier dirección)
     */
    public static function findExistingRoute(int $originId, int $destinationId): ?self
    {
        return self::where(function ($query) use ($originId, $destinationId) {
            $query->where('origin_id', $originId)
                  ->where('destination_id', $destinationId);
        })->orWhere(function ($query) use ($originId, $destinationId) {
            $query->where('origin_id', $destinationId)
                  ->where('destination_id', $originId);
        })->first();
    }

    /**
     * Crear o buscar ruta con cálculo automático de distancia
     */
    public static function createOrFind(int $originId, int $destinationId): self
    {
        // Buscar si ya existe
        $existingRoute = self::findExistingRoute($originId, $destinationId);
        
        if ($existingRoute) {
            return $existingRoute;
        }

        // Si no existe, crear nueva con cálculo automático
        $origin = Municipality::find($originId);
        $destination = Municipality::find($destinationId);
        
        if (!$origin || !$destination) {
            throw new \Exception('Municipios no encontrados');
        }

        $googleMaps = app(GoogleMapsService::class);
        $distanceData = $googleMaps->calculateDistance(
            $origin->name_municipality,
            $destination->name_municipality
        );

        $distanceKm = $distanceData ? $distanceData['distance_km'] : 0;

        return self::create([
            'origin_id' => $originId,
            'destination_id' => $destinationId,
            'distance_km' => $distanceKm,
            'status' => true
        ]);
    }
}
