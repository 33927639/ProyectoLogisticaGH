<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblRoute
 * 
 * @property int $id_route
 * @property int $id_origin
 * @property int $id_destination
 * @property float $distance_km
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property TblMunicipality $tbl_municipality
 * @property Collection|TblDelivery[] $tbl_deliveries
 *
 * @package App\Models
 */
class TblRoute extends Model
{
	protected $table = 'tbl_routes';
	protected $primaryKey = 'id_route';

	protected $casts = [
		'id_origin' => 'int',
		'id_destination' => 'int',
		'distance_km' => 'float',
		'status' => 'bool'
	];

	protected $fillable = [
		'id_origin',
		'id_destination',
		'distance_km',
		'status'
	];

	public function origin_municipality()
	{
		return $this->belongsTo(TblMunicipality::class, 'id_origin');
	}

	public function destination_municipality()
	{
		return $this->belongsTo(TblMunicipality::class, 'id_destination');
	}

	public function tbl_deliveries()
	{
		return $this->hasMany(TblDelivery::class, 'id_route');
	}
}
