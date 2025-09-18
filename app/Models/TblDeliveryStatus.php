<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblDeliveryStatus
 * 
 * @property int $id_status
 * @property string $name_status
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool|null $status
 * 
 * @property Collection|TblDelivery[] $tbl_deliveries
 *
 * @package App\Models
 */
class TblDeliveryStatus extends Model
{
	protected $table = 'tbl_delivery_statuses';
	protected $primaryKey = 'id_status';

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'name_status',
		'description',
		'status'
	];

	public function tbl_deliveries()
	{
		return $this->hasMany(TblDelivery::class, 'id_status');
	}
}
