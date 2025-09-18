<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Class Camion
 *
 * @property int $id
 * @property string $placa
 * @property string $marca
 * @property string $modelo
 * @property int $year
 * @property string|null $numero_motor
 * @property float $kilometraje_actual
 * @property int $intervalo_mantenimiento_km
 * @property string $estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Viaje[] $viajes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mantenimiento[] $mantenimientos
 * @property-read int|null $viajes_count
 * @property-read int|null $mantenimientos_count
 * @property-read bool $necesita_mantenimiento
 * @property-read int $kilometros_hasta_mantenimiento
 * @property-read \App\Models\Mantenimiento|null $ultimo_mantenimiento
 * @property-read \App\Models\Viaje|null $viaje_actual
 */
class Camion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'camiones';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'placa',
        'marca',
        'modelo',
        'year',
        'numero_motor',
        'kilometraje_actual',
        'intervalo_mantenimiento_km',
        'estado'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'kilometraje_actual' => 'decimal:2',
        'intervalo_mantenimiento_km' => 'integer',
        'year' => 'integer',
        'estado' => 'string'
    ];

    /**
     * Valid estados for the enum
     */
    public const ESTADO_ACTIVO = 'Activo';
    public const ESTADO_EN_TALLER = 'En Taller';
    public const ESTADO_INACTIVO = 'Inactivo';

    public const ESTADOS = [
        self::ESTADO_ACTIVO,
        self::ESTADO_EN_TALLER,
        self::ESTADO_INACTIVO
    ];

    /**
     * Validation rules
     * 
     * - placa: required, unique, max 20 characters
     * - marca: required, max 50 characters
     * - modelo: required, max 50 characters
     * - year: required, integer, between 1900 and current year + 1
     * - numero_motor: nullable, max 50 characters
     * - kilometraje_actual: required, numeric, min 0
     * - intervalo_mantenimiento_km: required, integer, min 1000, max 50000
     * - estado: required, in:Activo,En Taller,Inactivo
     */

    /**
     * Get the viajes for the camion.
     */
    public function viajes(): HasMany
    {
        return $this->hasMany(Viaje::class);
    }

    /**
     * Get the mantenimientos for the camion.
     */
    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class);
    }

    /**
     * Scope a query to only include active camiones.
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    /**
     * Scope a query to only include camiones en taller.
     */
    public function scopeEnTaller($query)
    {
        return $query->where('estado', self::ESTADO_EN_TALLER);
    }

    /**
     * Scope a query to only include inactive camiones.
     */
    public function scopeInactivos($query)
    {
        return $query->where('estado', self::ESTADO_INACTIVO);
    }

    /**
     * Scope a query to only include camiones that need maintenance.
     */
    public function scopeNecesitanMantenimiento($query)
    {
        return $query->whereRaw('kilometraje_actual >= (
            SELECT COALESCE(MAX(kilometraje_actual), 0) + intervalo_mantenimiento_km
            FROM mantemientos 
            WHERE camiones.id = mantemientos.camion_id 
            AND estado = "Completado"
        )');
    }

    /**
     * Mutator for placa - convert to uppercase
     */
    public function setPlacaAttribute($value)
    {
        $this->attributes['placa'] = strtoupper($value);
    }

    /**
     * Mutator for marca - convert to title case
     */
    public function setMarcaAttribute($value)
    {
        $this->attributes['marca'] = ucwords(strtolower($value));
    }

    /**
     * Mutator for modelo - convert to title case
     */
    public function setModeloAttribute($value)
    {
        $this->attributes['modelo'] = ucwords(strtolower($value));
    }

    /**
     * Accessor for formatted placa
     */
    public function getPlacaFormateadaAttribute()
    {
        return strtoupper($this->placa);
    }

    /**
     * Check if the camion needs maintenance based on current kilometrage
     */
    public function getNecesitaMantenimientoAttribute(): bool
    {
        $ultimoMantenimiento = $this->mantenimientos()
            ->where('estado', 'Completado')
            ->orderBy('fecha_realizada', 'desc')
            ->first();

        $kilometrajeUltimoMantenimiento = $ultimoMantenimiento 
            ? $this->kilometraje_actual - $this->intervalo_mantenimiento_km
            : 0;

        return ($this->kilometraje_actual - $kilometrajeUltimoMantenimiento) >= $this->intervalo_mantenimiento_km;
    }

    /**
     * Get kilometers until next maintenance
     */
    public function getKilometrosHastaMantenimientoAttribute(): int
    {
        $ultimoMantenimiento = $this->mantenimientos()
            ->where('estado', 'Completado')
            ->orderBy('fecha_realizada', 'desc')
            ->first();

        $kilometrajeUltimoMantenimiento = $ultimoMantenimiento 
            ? $this->kilometraje_actual - $this->intervalo_mantenimiento_km
            : 0;

        $kilometrosRecorridos = $this->kilometraje_actual - $kilometrajeUltimoMantenimiento;
        
        return max(0, $this->intervalo_mantenimiento_km - $kilometrosRecorridos);
    }

    /**
     * Get the last maintenance record
     */
    public function getUltimoMantenimientoAttribute()
    {
        return $this->mantenimientos()
            ->orderBy('fecha_realizada', 'desc')
            ->first();
    }

    /**
     * Get current active trip
     */
    public function getViajeActualAttribute()
    {
        return $this->viajes()
            ->where('estado', 'En Curso')
            ->first();
    }

    /**
     * Update the camion's current kilometrage
     */
    public function actualizarKilometraje(float $nuevoKilometraje): bool
    {
        if ($nuevoKilometraje < $this->kilometraje_actual) {
            return false;
        }

        $this->kilometraje_actual = $nuevoKilometraje;
        return $this->save();
    }

    /**
     * Check if camion is available for a new trip
     */
    public function estaDisponible(): bool
    {
        return $this->estado === self::ESTADO_ACTIVO && !$this->viaje_actual;
    }

    /**
     * Get the camion's display name
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->marca} {$this->modelo} ({$this->placa})";
    }
}