<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Class Viaje
 *
 * @property int $id
 * @property int $camion_id
 * @property int $piloto_id
 * @property int $ruta_id
 * @property float $kilometraje_inicial
 * @property float|null $kilometraje_final
 * @property Carbon $fecha_inicio
 * @property Carbon|null $fecha_fin
 * @property string $estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read \App\Models\Camion $camion
 * @property-read \App\Models\Piloto $piloto
 * @property-read \App\Models\Ruta $ruta
 * @property-read float|null $kilometros_recorridos
 * @property-read float|null $duracion_horas
 * @property-read string|null $duracion_formato
 * @property-read bool $esta_completado
 * @property-read bool $esta_en_curso
 * @property-read bool $esta_retrasado
 */
class Viaje extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'viajes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'camion_id',
        'piloto_id',
        'ruta_id',
        'kilometraje_inicial',
        'kilometraje_final',
        'fecha_inicio',
        'fecha_fin',
        'estado'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'kilometraje_inicial' => 'decimal:2',
        'kilometraje_final' => 'decimal:2',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'estado' => 'string'
    ];

    /**
     * Valid estados for the enum
     */
    public const ESTADO_PROGRAMADO = 'Programado';
    public const ESTADO_EN_CURSO = 'En Curso';
    public const ESTADO_COMPLETADO = 'Completado';
    public const ESTADO_CANCELADO = 'Cancelado';

    public const ESTADOS = [
        self::ESTADO_PROGRAMADO,
        self::ESTADO_EN_CURSO,
        self::ESTADO_COMPLETADO,
        self::ESTADO_CANCELADO
    ];

    /**
     * Validation rules
     * 
     * - camion_id: required, exists:camiones,id
     * - piloto_id: required, exists:pilotos,id
     * - ruta_id: required, exists:rutas,id
     * - kilometraje_inicial: required, numeric, min 0
     * - kilometraje_final: nullable, numeric, min:kilometraje_inicial
     * - fecha_inicio: required, date, after_or_equal:today
     * - fecha_fin: nullable, date, after:fecha_inicio
     * - estado: required, in:Programado,En Curso,Completado,Cancelado
     */

    /**
     * Get the camion that owns the viaje.
     */
    public function camion(): BelongsTo
    {
        return $this->belongsTo(Camion::class);
    }

    /**
     * Get the piloto that owns the viaje.
     */
    public function piloto(): BelongsTo
    {
        return $this->belongsTo(Piloto::class);
    }

    /**
     * Get the ruta that owns the viaje.
     */
    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class);
    }

    /**
     * Scope a query to only include programmed viajes.
     */
    public function scopeProgramados($query)
    {
        return $query->where('estado', self::ESTADO_PROGRAMADO);
    }

    /**
     * Scope a query to only include viajes en curso.
     */
    public function scopeEnCurso($query)
    {
        return $query->where('estado', self::ESTADO_EN_CURSO);
    }

    /**
     * Scope a query to only include completed viajes.
     */
    public function scopeCompletados($query)
    {
        return $query->where('estado', self::ESTADO_COMPLETADO);
    }

    /**
     * Scope a query to only include cancelled viajes.
     */
    public function scopeCancelados($query)
    {
        return $query->where('estado', self::ESTADO_CANCELADO);
    }

    /**
     * Scope a query to only include active viajes (programados y en curso).
     */
    public function scopeActivos($query)
    {
        return $query->whereIn('estado', [self::ESTADO_PROGRAMADO, self::ESTADO_EN_CURSO]);
    }

    /**
     * Scope a query to filter viajes by date range.
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope a query to filter viajes by camion.
     */
    public function scopePorCamion($query, $camionId)
    {
        return $query->where('camion_id', $camionId);
    }

    /**
     * Scope a query to filter viajes by piloto.
     */
    public function scopePorPiloto($query, $pilotoId)
    {
        return $query->where('piloto_id', $pilotoId);
    }

    /**
     * Scope a query to filter viajes by ruta.
     */
    public function scopePorRuta($query, $rutaId)
    {
        return $query->where('ruta_id', $rutaId);
    }

    /**
     * Scope a query to filter delayed trips.
     */
    public function scopeRetrasados($query)
    {
        return $query->where('estado', self::ESTADO_EN_CURSO)
            ->whereRaw('NOW() > DATE_ADD(fecha_inicio, INTERVAL (SELECT tiempo_estimado_horas FROM rutas WHERE id = viajes.ruta_id) HOUR)');
    }

    /**
     * Accessor for kilometers traveled
     */
    public function getKilometrosRecorridosAttribute(): ?float
    {
        if (!$this->kilometraje_final || !$this->kilometraje_inicial) {
            return null;
        }

        return $this->kilometraje_final - $this->kilometraje_inicial;
    }

    /**
     * Accessor for trip duration in hours
     */
    public function getDuracionHorasAttribute(): ?float
    {
        if (!$this->fecha_fin || !$this->fecha_inicio) {
            return null;
        }

        return $this->fecha_inicio->diffInHours($this->fecha_fin, true);
    }

    /**
     * Accessor for formatted duration
     */
    public function getDuracionFormatoAttribute(): ?string
    {
        if (!$this->duracion_horas) {
            return null;
        }

        $horas = floor($this->duracion_horas);
        $minutos = ($this->duracion_horas - $horas) * 60;

        if ($horas > 0 && $minutos > 0) {
            return $horas . 'h ' . round($minutos) . 'm';
        } elseif ($horas > 0) {
            return $horas . 'h';
        } else {
            return round($minutos) . 'm';
        }
    }

    /**
     * Check if viaje is completed
     */
    public function getEstaCompletadoAttribute(): bool
    {
        return $this->estado === self::ESTADO_COMPLETADO;
    }

    /**
     * Check if viaje is in progress
     */
    public function getEstaEnCursoAttribute(): bool
    {
        return $this->estado === self::ESTADO_EN_CURSO;
    }

    /**
     * Check if viaje is delayed
     */
    public function getEstaRetrasadoAttribute(): bool
    {
        if ($this->estado !== self::ESTADO_EN_CURSO || !$this->ruta || !$this->ruta->tiempo_estimado_horas) {
            return false;
        }

        $tiempoEstimadoLlegada = $this->fecha_inicio->copy()
            ->addHours((float) $this->ruta->tiempo_estimado_horas);

        return Carbon::now()->isAfter($tiempoEstimadoLlegada);
    }

    /**
     * Get estimated arrival time
     */
    public function getHoraEstimadaLlegadaAttribute(): ?Carbon
    {
        if (!$this->ruta || !$this->ruta->tiempo_estimado_horas) {
            return null;
        }
        
        return $this->fecha_inicio->copy()
            ->addHours((float) $this->ruta->tiempo_estimado_horas);
    }

    /**
     * Get trip progress percentage
     */
    public function getPorcentajeProgresoAttribute(): ?float
    {
        if ($this->estado === self::ESTADO_COMPLETADO) {
            return 100;
        }

        if ($this->estado !== self::ESTADO_EN_CURSO || !$this->ruta) {
            return 0;
        }

        $tiempoTranscurrido = $this->fecha_inicio->diffInHours(Carbon::now(), true);
        $tiempoEstimadoTotal = (float) $this->ruta->tiempo_estimado_horas;

        if ($tiempoEstimadoTotal <= 0) {
            return 0;
        }

        return min(100, round(($tiempoTranscurrido / $tiempoEstimadoTotal) * 100, 2));
    }

    /**
     * Get trip efficiency percentage
     */
    public function getEficienciaAttribute(): ?float
    {
        if (!$this->esta_completado || !$this->duracion_horas) {
            return null;
        }

        if ($this->duracion_horas <= 0) {
            return null;
        }

        return round(((float) $this->ruta->tiempo_estimado_horas / $this->duracion_horas) * 100, 2);
    }

    /**
     * Get time remaining for trip in progress
     */
    public function getTiempoRestanteAttribute(): ?string
    {
        if ($this->estado !== self::ESTADO_EN_CURSO || !$this->ruta) {
            return null;
        }

        $horaEstimadaLlegada = $this->hora_estimada_llegada;
        if (!$horaEstimadaLlegada) {
            return null;
        }
        
        $ahora = Carbon::now();

        if ($ahora->isAfter($horaEstimadaLlegada)) {
            $retraso = $ahora->diffInHours($horaEstimadaLlegada, true);
            return 'Retrasado ' . round($retraso, 1) . 'h';
        }

        $tiempoRestante = $ahora->diffInHours($horaEstimadaLlegada, true);
        
        if ($tiempoRestante >= 1) {
            return round($tiempoRestante, 1) . 'h restantes';
        } else {
            return round($tiempoRestante * 60) . 'm restantes';
        }
    }

    /**
     * Start the trip
     */
    public function iniciar(): bool
    {
        if ($this->estado !== self::ESTADO_PROGRAMADO) {
            return false;
        }

        $this->estado = self::ESTADO_EN_CURSO;
        $this->fecha_inicio = Carbon::now();
        
        return $this->save();
    }

    /**
     * Complete the trip
     */
    public function completar(float $kilometrajeFinal): bool
    {
        if ($this->estado !== self::ESTADO_EN_CURSO) {
            return false;
        }

        if ($kilometrajeFinal < $this->kilometraje_inicial) {
            return false;
        }

        $this->estado = self::ESTADO_COMPLETADO;
        $this->fecha_fin = Carbon::now();
        $this->kilometraje_final = $kilometrajeFinal;

        // Update camion's current kilometrage
        $this->camion->actualizarKilometraje($kilometrajeFinal);

        return $this->save();
    }

    /**
     * Cancel the trip
     */
    public function cancelar(): bool
    {
        if ($this->estado === self::ESTADO_COMPLETADO) {
            return false;
        }

        $this->estado = self::ESTADO_CANCELADO;
        
        return $this->save();
    }

    /**
     * Get trip summary for display
     */
    public function getResumenAttribute(): string
    {
        return sprintf(
            '%s → %s | %s (%s) | Piloto: %s',
            $this->ruta->origen,
            $this->ruta->destino,
            $this->camion->placa,
            $this->camion->marca,
            $this->piloto->nombre_completo
        );
    }

    /**
     * Check if all required resources are available
     */
    public function recursosDisponibles(): bool
    {
        return $this->camion->estaDisponible() && 
               $this->piloto->esta_disponible && 
               $this->ruta->estado === 'Activa';
    }
}