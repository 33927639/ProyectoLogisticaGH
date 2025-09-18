<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Camione
 * 
 * @property int $id
 * @property string $placa
 * @property string $marca
 * @property string $modelo
 * @property Carbon $year
 * @property string|null $numero_motor
 * @property float $kilometraje_actual
 * @property int $intervalo_mantenimiento_km
 * @property string $estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Mantemiento[] $mantemientos
 * @property Collection|Viaje[] $viajes
 *
 * @package App\Models
 */
class Camione extends Model
{
	protected $table = 'camiones';

	protected $casts = [
		'year' => 'datetime',
		'kilometraje_actual' => 'float',
		'intervalo_mantenimiento_km' => 'int'
	];

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

	public function mantemientos()
	{
		return $this->hasMany(Mantemiento::class, 'camion_id');
	}

	public function viajes()
	{
		return $this->hasMany(Viaje::class, 'camion_id');
	}
}
