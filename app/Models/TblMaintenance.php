<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblMaintenance
 * 
 * @property int $id_maintenance
 * @property int $id_vehicle
 * @property string $type
 * @property Carbon $maintenance_date
 * @property bool|null $approved
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property TblVehicle $tbl_vehicle
 *
 * @package App\Models
 */
class TblMaintenance extends Model
{
	protected $table = 'tbl_maintenances';
	protected $primaryKey = 'id_maintenance';

	protected $casts = [
		'id_vehicle' => 'int',
		'maintenance_date' => 'datetime',
		'approved' => 'bool',
		'status' => 'bool'
	];

	protected $fillable = [
		'id_vehicle',
		'type',
		'maintenance_date',
		'approved',
		'status'
	];

	public function tbl_vehicle()
	{
		return $this->belongsTo(TblVehicle::class, 'id_vehicle');
	}
}
