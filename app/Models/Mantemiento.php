<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Mantemiento
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
 * @property Camione $camione
 *
 * @package App\Models
 */
class Mantemiento extends Model
{
	protected $table = 'mantemientos';

	protected $casts = [
		'camion_id' => 'int',
		'fecha_programada' => 'datetime',
		'fecha_realizada' => 'datetime',
		'costo' => 'float'
	];

	protected $fillable = [
		'camion_id',
		'tipo_mantenimiento',
		'descripcion',
		'fecha_programada',
		'fecha_realizada',
		'costo',
		'estado'
	];

	public function camione()
	{
		return $this->belongsTo(Camione::class, 'camion_id');
	}
}
