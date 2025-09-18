<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblDriver
 * 
 * @property int $id_driver
 * @property string|null $name
 * @property string $license
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|TblDeliveryAssignment[] $tbl_delivery_assignments
 *
 * @package App\Models
 */
class TblDriver extends Model
{
	protected $table = 'tbl_drivers';
	protected $primaryKey = 'id_driver';

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'name',
		'license',
		'status'
	];

	public function tbl_delivery_assignments()
	{
		return $this->hasMany(TblDeliveryAssignment::class, 'id_driver');
	}
}
