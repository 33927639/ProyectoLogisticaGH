<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblKilometer
 * 
 * @property int $id_kilometer
 * @property int $id_delivery
 * @property int $id_vehicle
 * @property float $kilometers_traveled
 * @property int|null $id_alert
 * @property Carbon $record_date
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property TblAlertStatus|null $tbl_alert_status
 * @property TblDelivery $tbl_delivery
 * @property TblVehicle $tbl_vehicle
 *
 * @package App\Models
 */
class TblKilometer extends Model
{
	protected $table = 'tbl_kilometers';
	protected $primaryKey = 'id_kilometer';

	protected $casts = [
		'id_delivery' => 'int',
		'id_vehicle' => 'int',
		'kilometers_traveled' => 'float',
		'id_alert' => 'int',
		'record_date' => 'datetime',
		'status' => 'bool'
	];

	protected $fillable = [
		'id_delivery',
		'id_vehicle',
		'kilometers_traveled',
		'id_alert',
		'record_date',
		'status'
	];

	public function tbl_alert_status()
	{
		return $this->belongsTo(TblAlertStatus::class, 'id_alert');
	}

	public function tbl_delivery()
	{
		return $this->belongsTo(TblDelivery::class, 'id_delivery');
	}

	public function tbl_vehicle()
	{
		return $this->belongsTo(TblVehicle::class, 'id_vehicle');
	}
}
