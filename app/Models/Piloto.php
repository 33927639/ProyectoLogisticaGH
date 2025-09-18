<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Class Piloto
 *
 * @property int $id
 * @property string $nombre
 * @property string $apellido
 * @property string $licencia
 * @property string|null $telefono
 * @property string|null $email
 * @property string $estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Viaje[] $viajes
 * @property-read int|null $viajes_count
 * @property-read string $nombre_completo
 * @property-read \App\Models\Viaje|null $viaje_actual
 * @property-read bool $esta_disponible
 */
class Piloto extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'pilotos';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'licencia',
        'telefono',
        'email',
        'estado'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'estado' => 'string'
    ];

    /**
     * Valid estados for the enum
     */
    public const ESTADO_ACTIVO = 'Activo';
    public const ESTADO_INACTIVO = 'Inactivo';
    public const ESTADO_SUSPENDIDO = 'Suspendido';

    public const ESTADOS = [
        self::ESTADO_ACTIVO,
        self::ESTADO_INACTIVO,
        self::ESTADO_SUSPENDIDO
    ];

    /**
     * Validation rules
     * 
     * - nombre: required, string, max 255 characters
     * - apellido: required, string, max 255 characters
     * - licencia: required, unique, string, max 50 characters
     * - telefono: nullable, string, max 20 characters
     * - email: nullable, unique, email, max 255 characters
     * - estado: required, in:Activo,Inactivo,Suspendido
     */

    /**
     * Get the viajes for the piloto.
     */
    public function viajes(): HasMany
    {
        return $this->hasMany(Viaje::class);
    }

    /**
     * Scope a query to only include active pilotos.
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    /**
     * Scope a query to only include inactive pilotos.
     */
    public function scopeInactivos($query)
    {
        return $query->where('estado', self::ESTADO_INACTIVO);
    }

    /**
     * Scope a query to only include suspended pilotos.
     */
    public function scopeSuspendidos($query)
    {
        return $query->where('estado', self::ESTADO_SUSPENDIDO);
    }

    /**
     * Scope a query to only include available pilotos (active and not on a current trip).
     */
    public function scopeDisponibles($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO)
            ->whereDoesntHave('viajes', function ($q) {
                $q->where('estado', 'En Curso');
            });
    }

    /**
     * Mutator for nombre - convert to title case
     */
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = ucwords(strtolower(trim($value)));
    }

    /**
     * Mutator for apellido - convert to title case
     */
    public function setApellidoAttribute($value)
    {
        $this->attributes['apellido'] = ucwords(strtolower(trim($value)));
    }

    /**
     * Mutator for licencia - convert to uppercase
     */
    public function setLicenciaAttribute($value)
    {
        $this->attributes['licencia'] = strtoupper(trim($value));
    }

    /**
     * Mutator for email - convert to lowercase
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value ? strtolower(trim($value)) : null;
    }

    /**
     * Mutator for telefono - clean format
     */
    public function setTelefonoAttribute($value)
    {
        $this->attributes['telefono'] = $value ? preg_replace('/[^0-9+\-\s]/', '', $value) : null;
    }

    /**
     * Accessor for full name
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre . ' ' . $this->apellido);
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
     * Check if piloto is available for a new trip
     */
    public function getEstaDisponibleAttribute(): bool
    {
        return $this->estado === self::ESTADO_ACTIVO && !$this->viaje_actual;
    }

    /**
     * Get piloto's initials
     */
    public function getInicialesAttribute(): string
    {
        $nombre = substr($this->nombre, 0, 1);
        $apellido = substr($this->apellido, 0, 1);
        return strtoupper($nombre . $apellido);
    }

    /**
     * Get total completed trips
     */
    public function getTotalViajesCompletadosAttribute(): int
    {
        return $this->viajes()
            ->where('estado', 'Completado')
            ->count();
    }

    /**
     * Get total kilometers driven
     */
    public function getTotalKilometrosRecorridosAttribute(): float
    {
        return $this->viajes()
            ->where('estado', 'Completado')
            ->whereNotNull('kilometraje_final')
            ->get()
            ->sum(function ($viaje) {
                return $viaje->kilometraje_final - $viaje->kilometraje_inicial;
            });
    }

    /**
     * Get piloto's experience level based on completed trips
     */
    public function getNivelExperienciaAttribute(): string
    {
        $viajesCompletados = $this->total_viajes_completados;

        if ($viajesCompletados >= 100) {
            return 'Experto';
        } elseif ($viajesCompletados >= 50) {
            return 'Avanzado';
        } elseif ($viajesCompletados >= 20) {
            return 'Intermedio';
        } elseif ($viajesCompletados >= 5) {
            return 'Principiante';
        }

        return 'Nuevo';
    }

    /**
     * Get formatted phone number
     */
    public function getTelefonoFormateadoAttribute(): string
    {
        if (!$this->telefono) {
            return 'No registrado';
        }

        // Simple formatting for Guatemala phone numbers (assuming 8-digit format)
        $clean = preg_replace('/[^0-9]/', '', $this->telefono);
        
        if (strlen($clean) === 8) {
            return substr($clean, 0, 4) . '-' . substr($clean, 4);
        }

        return $this->telefono;
    }

    /**
     * Check if piloto can be assigned to a specific camion
     */
    public function puedeConducir(Camion $camion): bool
    {
        return $this->esta_disponible && $camion->estaDisponible();
    }

    /**
     * Get piloto's current status display
     */
    public function getEstadoDisplayAttribute(): string
    {
        $status = $this->estado;
        
        if ($this->estado === self::ESTADO_ACTIVO && $this->viaje_actual) {
            $status .= ' (En viaje)';
        }

        return $status;
    }
}