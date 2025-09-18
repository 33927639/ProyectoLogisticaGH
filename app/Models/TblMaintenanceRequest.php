<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblMaintenanceRequest
 * 
 * @property int $id_request
 * @property int $id_vehicle
 * @property Carbon $request_date
 * @property string $reason
 * @property int|null $approved_by
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property TblUser|null $tbl_user
 * @property TblVehicle $tbl_vehicle
 *
 * @package App\Models
 */
class TblMaintenanceRequest extends Model
{
	protected $table = 'tbl_maintenance_requests';
	protected $primaryKey = 'id_request';

	protected $casts = [
		'id_vehicle' => 'int',
		'request_date' => 'datetime',
		'approved_by' => 'int',
		'status' => 'bool'
	];

	protected $fillable = [
		'id_vehicle',
		'request_date',
		'reason',
		'approved_by',
		'status'
	];

	public function tbl_user()
	{
		return $this->belongsTo(TblUser::class, 'approved_by');
	}

	public function tbl_vehicle()
	{
		return $this->belongsTo(TblVehicle::class, 'id_vehicle');
	}
}
