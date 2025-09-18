<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Class Ruta
 *
 * @property int $id
 * @property string $origen
 * @property string $destino
 * @property float $distancia_km
 * @property float $tiempo_estimado_horas
 * @property string|null $descripcion
 * @property string $estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Viaje[] $viajes
 * @property-read int|null $viajes_count
 * @property-read string $nombre_completo
 * @property-read string $tiempo_estimado_formato
 * @property-read float $velocidad_promedio
 */
class Ruta extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'rutas';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'origen',
        'destino',
        'distancia_km',
        'tiempo_estimado_horas',
        'descripcion',
        'estado'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'distancia_km' => 'decimal:2',
        'tiempo_estimado_horas' => 'decimal:2',
        'estado' => 'string'
    ];

    /**
     * Valid estados for the enum
     */
    public const ESTADO_ACTIVA = 'Activa';
    public const ESTADO_INACTIVA = 'Inactiva';

    public const ESTADOS = [
        self::ESTADO_ACTIVA,
        self::ESTADO_INACTIVA
    ];

    /**
     * Validation rules
     * 
     * - origen: required, string, max 255 characters
     * - destino: required, string, max 255 characters
     * - distancia_km: required, numeric, min 0.1, max 9999.99
     * - tiempo_estimado_horas: required, numeric, min 0.1, max 99.99
     * - descripcion: nullable, string, max 1000 characters
     * - estado: required, in:Activa,Inactiva
     */

    /**
     * Get the viajes for the ruta.
     */
    public function viajes(): HasMany
    {
        return $this->hasMany(Viaje::class);
    }

    /**
     * Scope a query to only include active rutas.
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVA);
    }

    /**
     * Scope a query to only include inactive rutas.
     */
    public function scopeInactivas($query)
    {
        return $query->where('estado', self::ESTADO_INACTIVA);
    }

    /**
     * Scope a query to filter rutas by origen.
     */
    public function scopePorOrigen($query, $origen)
    {
        return $query->where('origen', 'like', '%' . $origen . '%');
    }

    /**
     * Scope a query to filter rutas by destino.
     */
    public function scopePorDestino($query, $destino)
    {
        return $query->where('destino', 'like', '%' . $destino . '%');
    }

    /**
     * Scope a query to filter rutas by maximum distance.
     */
    public function scopeDistanciaMaxima($query, $distanciaMaxima)
    {
        return $query->where('distancia_km', '<=', $distanciaMaxima);
    }

    /**
     * Scope a query to filter rutas by maximum estimated time.
     */
    public function scopeTiempoMaximo($query, $tiempoMaximo)
    {
        return $query->where('tiempo_estimado_horas', '<=', $tiempoMaximo);
    }

    /**
     * Mutator for origen - convert to title case
     */
    public function setOrigenAttribute($value)
    {
        $this->attributes['origen'] = ucwords(strtolower(trim($value)));
    }

    /**
     * Mutator for destino - convert to title case
     */
    public function setDestinoAttribute($value)
    {
        $this->attributes['destino'] = ucwords(strtolower(trim($value)));
    }

    /**
     * Accessor for complete route name
     */
    public function getNombreCompletoAttribute(): string
    {
        return $this->origen . ' → ' . $this->destino;
    }

    /**
     * Accessor for formatted estimated time
     */
    public function getTiempoEstimadoFormatoAttribute(): string
    {
        $horas = floor($this->tiempo_estimado_horas);
        $minutos = ($this->tiempo_estimado_horas - $horas) * 60;

        if ($horas > 0 && $minutos > 0) {
            return $horas . 'h ' . round($minutos) . 'm';
        } elseif ($horas > 0) {
            return $horas . 'h';
        } else {
            return round($minutos) . 'm';
        }
    }

    /**
     * Accessor for average speed (km/h)
     */
    public function getVelocidadPromedioAttribute(): float
    {
        if ($this->tiempo_estimado_horas <= 0) {
            return 0;
        }

        return round($this->distancia_km / $this->tiempo_estimado_horas, 2);
    }

    /**
     * Get formatted distance
     */
    public function getDistanciaFormateadaAttribute(): string
    {
        return number_format($this->distancia_km, 2) . ' km';
    }

    /**
     * Get total completed trips for this route
     */
    public function getTotalViajesCompletadosAttribute(): int
    {
        return $this->viajes()
            ->where('estado', 'Completado')
            ->count();
    }

    /**
     * Get average actual time for completed trips on this route
     */
    public function getTiempoPromedioRealAttribute(): ?float
    {
        $viajesCompletados = $this->viajes()
            ->where('estado', 'Completado')
            ->whereNotNull('fecha_fin')
            ->get();

        if ($viajesCompletados->isEmpty()) {
            return null;
        }

        $tiempoTotal = $viajesCompletados->sum(function ($viaje) {
            return Carbon::parse($viaje->fecha_fin)->diffInHours(Carbon::parse($viaje->fecha_inicio), true);
        });

        return round($tiempoTotal / $viajesCompletados->count(), 2);
    }

    /**
     * Get route efficiency percentage (estimated vs actual time)
     */
    public function getEficienciaRutaAttribute(): ?float
    {
        $tiempoPromedioReal = $this->tiempo_promedio_real;
        
        if (!$tiempoPromedioReal || $tiempoPromedioReal <= 0) {
            return null;
        }

        return round(($this->tiempo_estimado_horas / $tiempoPromedioReal) * 100, 2);
    }

    /**
     * Check if this is a long distance route (over 200 km)
     */
    public function esRutaLargaAttribute(): bool
    {
        return $this->distancia_km > 200;
    }

    /**
     * Get route difficulty based on distance and time
     */
    public function getDificultadAttribute(): string
    {
        $velocidadPromedio = $this->velocidad_promedio;

        if ($this->distancia_km > 500) {
            return 'Muy Difícil';
        } elseif ($this->distancia_km > 300 || $velocidadPromedio < 40) {
            return 'Difícil';
        } elseif ($this->distancia_km > 150 || $velocidadPromedio < 60) {
            return 'Moderada';
        } else {
            return 'Fácil';
        }
    }

    /**
     * Get estimated fuel consumption (assuming average consumption)
     */
    public function getConsumoEstimadoCombustibleAttribute(): float
    {
        // Assuming 35 liters per 100 km for trucks
        $consumoPor100Km = 35;
        return round(($this->distancia_km * $consumoPor100Km) / 100, 2);
    }

    /**
     * Find reverse route (destino -> origen)
     */
    public function getRutaInversaAttribute()
    {
        return static::where('origen', $this->destino)
            ->where('destino', $this->origen)
            ->where('estado', self::ESTADO_ACTIVA)
            ->first();
    }

    /**
     * Check if route has a reverse route available
     */
    public function tieneRutaInversaAttribute(): bool
    {
        return $this->ruta_inversa !== null;
    }

    /**
     * Get routes from the same origin
     */
    public function rutasSimilares()
    {
        return static::where('origen', $this->origen)
            ->where('id', '!=', $this->id)
            ->where('estado', self::ESTADO_ACTIVA)
            ->orderBy('destino');
    }

    /**
     * Calculate estimated arrival time from a given start time
     */
    public function calcularHoraLlegada(Carbon $horaInicio): Carbon
    {
        return $horaInicio->copy()->addHours($this->tiempo_estimado_horas);
    }
}