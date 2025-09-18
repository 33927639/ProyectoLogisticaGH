<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Class Mantenimiento
 *
 * @property int $id
 * @property int $camion_id
 * @property string $tipo_mantenimiento
 * @property string|null $descripcion
 * @property Carbon $fecha_programada
 * @property Carbon|null $fecha_realizada
 * @property float|null $costo
 * @property string $estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read \App\Models\Camion $camion
 * @property-read bool $esta_vencido
 * @property-read bool $esta_proximo
 * @property-read int|null $dias_hasta_vencimiento
 * @property-read string $costo_formateado
 */
class Mantenimiento extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * Note: The migration file has 'mantemientos' (with typo), but we'll use the correct spelling
     */
    protected $table = 'mantemientos';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'camion_id',
        'tipo_mantenimiento',
        'descripcion',
        'fecha_programada',
        'fecha_realizada',
        'costo',
        'estado'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'fecha_programada' => 'date',
        'fecha_realizada' => 'date',
        'costo' => 'decimal:2',
        'estado' => 'string'
    ];

    /**
     * Valid estados for the enum
     */
    public const ESTADO_PROGRAMADO = 'Programado';
    public const ESTADO_EN_PROCESO = 'En Proceso';
    public const ESTADO_COMPLETADO = 'Completado';
    public const ESTADO_CANCELADO = 'Cancelado';

    public const ESTADOS = [
        self::ESTADO_PROGRAMADO,
        self::ESTADO_EN_PROCESO,
        self::ESTADO_COMPLETADO,
        self::ESTADO_CANCELADO
    ];

    /**
     * Common maintenance types
     */
    public const TIPO_PREVENTIVO = 'Preventivo';
    public const TIPO_CORRECTIVO = 'Correctivo';
    public const TIPO_CAMBIO_ACEITE = 'Cambio de Aceite';
    public const TIPO_CAMBIO_FILTROS = 'Cambio de Filtros';
    public const TIPO_REVISION_FRENOS = 'Revisión de Frenos';
    public const TIPO_CAMBIO_LLANTAS = 'Cambio de Llantas';
    public const TIPO_REVISION_MOTOR = 'Revisión de Motor';
    public const TIPO_MANTENIMIENTO_GENERAL = 'Mantenimiento General';

    public const TIPOS_MANTENIMIENTO = [
        self::TIPO_PREVENTIVO,
        self::TIPO_CORRECTIVO,
        self::TIPO_CAMBIO_ACEITE,
        self::TIPO_CAMBIO_FILTROS,
        self::TIPO_REVISION_FRENOS,
        self::TIPO_CAMBIO_LLANTAS,
        self::TIPO_REVISION_MOTOR,
        self::TIPO_MANTENIMIENTO_GENERAL
    ];

    /**
     * Validation rules
     * 
     * - camion_id: required, exists:camiones,id
     * - tipo_mantenimiento: required, string, max 255 characters
     * - descripcion: nullable, string, max 1000 characters
     * - fecha_programada: required, date, after_or_equal:today
     * - fecha_realizada: nullable, date, after_or_equal:fecha_programada
     * - costo: nullable, numeric, min 0, max 999999.99
     * - estado: required, in:Programado,En Proceso,Completado,Cancelado
     */

    /**
     * Get the camion that owns the mantenimiento.
     */
    public function camion(): BelongsTo
    {
        return $this->belongsTo(Camion::class);
    }

    /**
     * Scope a query to only include programmed mantenimientos.
     */
    public function scopeProgramados($query)
    {
        return $query->where('estado', self::ESTADO_PROGRAMADO);
    }

    /**
     * Scope a query to only include mantenimientos en proceso.
     */
    public function scopeEnProceso($query)
    {
        return $query->where('estado', self::ESTADO_EN_PROCESO);
    }

    /**
     * Scope a query to only include completed mantenimientos.
     */
    public function scopeCompletados($query)
    {
        return $query->where('estado', self::ESTADO_COMPLETADO);
    }

    /**
     * Scope a query to only include cancelled mantenimientos.
     */
    public function scopeCancelados($query)
    {
        return $query->where('estado', self::ESTADO_CANCELADO);
    }

    /**
     * Scope a query to only include pending mantenimientos (programados y en proceso).
     */
    public function scopePendientes($query)
    {
        return $query->whereIn('estado', [self::ESTADO_PROGRAMADO, self::ESTADO_EN_PROCESO]);
    }

    /**
     * Scope a query to filter overdue maintenance.
     */
    public function scopeVencidos($query)
    {
        return $query->where('estado', self::ESTADO_PROGRAMADO)
            ->where('fecha_programada', '<', Carbon::today());
    }

    /**
     * Scope a query to filter maintenance due soon.
     */
    public function scopeProximos($query, $dias = 7)
    {
        return $query->where('estado', self::ESTADO_PROGRAMADO)
            ->whereBetween('fecha_programada', [
                Carbon::today(),
                Carbon::today()->addDays($dias)
            ]);
    }

    /**
     * Scope a query to filter by maintenance type.
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_mantenimiento', 'like', '%' . $tipo . '%');
    }

    /**
     * Scope a query to filter by camion.
     */
    public function scopePorCamion($query, $camionId)
    {
        return $query->where('camion_id', $camionId);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_programada', [$fechaInicio, $fechaFin]);
    }

    /**
     * Mutator for tipo_mantenimiento - convert to title case
     */
    public function setTipoMantenimientoAttribute($value)
    {
        $this->attributes['tipo_mantenimiento'] = ucwords(strtolower(trim($value)));
    }

    /**
     * Check if maintenance is overdue
     */
    public function getEstaVencidoAttribute(): bool
    {
        return $this->estado === self::ESTADO_PROGRAMADO && 
               $this->fecha_programada->isPast();
    }

    /**
     * Check if maintenance is due soon (within 7 days)
     */
    public function getEstaProximoAttribute(): bool
    {
        return $this->estado === self::ESTADO_PROGRAMADO && 
               $this->fecha_programada->isBetween(Carbon::today(), Carbon::today()->addDays(7));
    }

    /**
     * Get days until due date
     */
    public function getDiasHastaVencimientoAttribute(): ?int
    {
        if ($this->estado !== self::ESTADO_PROGRAMADO) {
            return null;
        }

        return Carbon::today()->diffInDays($this->fecha_programada, false);
    }

    /**
     * Get formatted cost
     */
    public function getCostoFormateadoAttribute(): string
    {
        if (!$this->costo) {
            return 'No especificado';
        }

        return 'Q. ' . number_format($this->costo, 2);
    }

    /**
     * Get maintenance priority based on due date and type
     */
    public function getPrioridadAttribute(): string
    {
        if ($this->estado !== self::ESTADO_PROGRAMADO) {
            return 'N/A';
        }

        if ($this->esta_vencido) {
            return 'Urgente';
        }

        if ($this->esta_proximo) {
            return 'Alta';
        }

        $diasHasta = $this->dias_hasta_vencimiento;

        if ($diasHasta <= 14) {
            return 'Media';
        }

        return 'Baja';
    }

    /**
     * Get status display with additional info
     */
    public function getEstadoDisplayAttribute(): string
    {
        $estado = $this->estado;

        if ($this->estado === self::ESTADO_PROGRAMADO) {
            if ($this->esta_vencido) {
                $estado .= ' (Vencido)';
            } elseif ($this->esta_proximo) {
                $estado .= ' (Próximo)';
            }
        }

        return $estado;
    }

    /**
     * Get days since completion
     */
    public function getDiasDesdeCompletadoAttribute(): ?int
    {
        if (!$this->fecha_realizada) {
            return null;
        }

        return $this->fecha_realizada->diffInDays(Carbon::today());
    }

    /**
     * Check if maintenance is preventive
     */
    public function esPreventivoAttribute(): bool
    {
        return stripos($this->tipo_mantenimiento, 'preventivo') !== false;
    }

    /**
     * Check if maintenance is corrective
     */
    public function esCorrectivoAttribute(): bool
    {
        return stripos($this->tipo_mantenimiento, 'correctivo') !== false;
    }

    /**
     * Get maintenance duration (if completed)
     */
    public function getDuracionDiasAttribute(): ?int
    {
        if (!$this->fecha_realizada) {
            return null;
        }

        return $this->fecha_programada->diffInDays($this->fecha_realizada, false);
    }

    /**
     * Start maintenance
     */
    public function iniciar(): bool
    {
        if ($this->estado !== self::ESTADO_PROGRAMADO) {
            return false;
        }

        $this->estado = self::ESTADO_EN_PROCESO;
        
        // Update camion status to "En Taller" if not already
        if ($this->camion->estado === Camion::ESTADO_ACTIVO) {
            $this->camion->update(['estado' => Camion::ESTADO_EN_TALLER]);
        }

        return $this->save();
    }

    /**
     * Complete maintenance
     */
    public function completar(float $costo = null, string $observaciones = null): bool
    {
        if (!in_array($this->estado, [self::ESTADO_PROGRAMADO, self::ESTADO_EN_PROCESO])) {
            return false;
        }

        $this->estado = self::ESTADO_COMPLETADO;
        $this->fecha_realizada = Carbon::today();
        
        if ($costo !== null) {
            $this->costo = $costo;
        }

        if ($observaciones !== null) {
            $this->descripcion = $observaciones;
        }

        // Update camion status back to "Activo" if no other pending maintenance
        $otrosMantenimientosPendientes = static::where('camion_id', $this->camion_id)
            ->where('id', '!=', $this->id)
            ->whereIn('estado', [self::ESTADO_PROGRAMADO, self::ESTADO_EN_PROCESO])
            ->exists();

        if (!$otrosMantenimientosPendientes && $this->camion->estado === Camion::ESTADO_EN_TALLER) {
            $this->camion->update(['estado' => Camion::ESTADO_ACTIVO]);
        }

        return $this->save();
    }

    /**
     * Cancel maintenance
     */
    public function cancelar(): bool
    {
        if ($this->estado === self::ESTADO_COMPLETADO) {
            return false;
        }

        $this->estado = self::ESTADO_CANCELADO;

        // Update camion status back to "Activo" if no other pending maintenance
        $otrosMantenimientosPendientes = static::where('camion_id', $this->camion_id)
            ->where('id', '!=', $this->id)
            ->whereIn('estado', [self::ESTADO_PROGRAMADO, self::ESTADO_EN_PROCESO])
            ->exists();

        if (!$otrosMantenimientosPendientes && $this->camion->estado === Camion::ESTADO_EN_TALLER) {
            $this->camion->update(['estado' => Camion::ESTADO_ACTIVO]);
        }

        return $this->save();
    }

    /**
     * Reschedule maintenance to a new date
     */
    public function reprogramar(Carbon $nuevaFecha): bool
    {
        if ($this->estado !== self::ESTADO_PROGRAMADO) {
            return false;
        }

        $this->fecha_programada = $nuevaFecha;
        
        return $this->save();
    }

    /**
     * Get maintenance summary for display
     */
    public function getResumenAttribute(): string
    {
        return sprintf(
            '%s - %s (%s) | %s',
            $this->tipo_mantenimiento,
            $this->camion->placa,
            $this->camion->marca,
            $this->fecha_programada->format('d/m/Y')
        );
    }
}