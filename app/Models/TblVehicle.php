<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblVehicle
 *
 * @property int $id_vehicle
 * @property string $license_plate
 * @property int $capacity
 * @property string $plates
 * @property bool $available
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|TblDeliveryAssignment[] $tbl_delivery_assignments
 * @property Collection|TblExpense[] $tbl_expenses
 * @property Collection|TblKilometer[] $tbl_kilometers
 * @property Collection|TblMaintenanceRequest[] $tbl_maintenance_requests
 * @property Collection|TblMaintenance[] $tbl_maintenances
 *
 * @package App\Models
 */
class TblVehicle extends Model
{
	protected $table = 'tbl_vehicles';
	protected $primaryKey = 'id_vehicle';

	protected $casts = [
		'capacity' => 'int',
		'available' => 'bool',
		'status' => 'bool'
	];

	protected $fillable = [
		'license_plate',
		'capacity',
		'plates',
		'available',
		'status'
	];

	public function tbl_delivery_assignments()
	{
		return $this->hasMany(TblDeliveryAssignment::class, 'id_vehicle');
	}

	public function tbl_expenses()
	{
		return $this->hasMany(TblExpense::class, 'id_vehicle');
	}

	public function tbl_kilometers()
	{
		return $this->hasMany(TblKilometer::class, 'id_vehicle');
	}

	public function tbl_maintenance_requests()
	{
		return $this->hasMany(TblMaintenanceRequest::class, 'id_vehicle');
	}

	public function tbl_maintenances()
	{
		return $this->hasMany(TblMaintenance::class, 'id_vehicle');
	}
}
