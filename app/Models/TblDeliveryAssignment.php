<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblDeliveryAssignment
 * 
 * @property int $id_delivery
 * @property int $id_vehicle
 * @property int $id_driver
 * @property Carbon $assignment_date
 * 
 * @property TblDelivery $tbl_delivery
 * @property TblDriver $tbl_driver
 * @property TblVehicle $tbl_vehicle
 *
 * @package App\Models
 */
class TblDeliveryAssignment extends Model
{
	protected $table = 'tbl_delivery_assignments';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_delivery' => 'int',
		'id_vehicle' => 'int',
		'id_driver' => 'int',
		'assignment_date' => 'datetime'
	];

	protected $fillable = [
		'assignment_date'
	];

	public function tbl_delivery()
	{
		return $this->belongsTo(TblDelivery::class, 'id_delivery');
	}

	public function tbl_driver()
	{
		return $this->belongsTo(TblDriver::class, 'id_driver');
	}

	public function tbl_vehicle()
	{
		return $this->belongsTo(TblVehicle::class, 'id_vehicle');
	}
}
