<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblAlertStatus
 *
 * @property int $id_alert
 * @property string $name_alert
 * @property string|null $description
 * @property float $threshold_km
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|TblKilometer[] $tbl_kilometers
 *
 * @package App\Models
 */
class TblAlertStatus extends Model
{
	protected $table = 'tbl_alert_statuses';
	protected $primaryKey = 'id_alert';

	protected $casts = [
		'threshold_km' => 'float'
	];

	protected $fillable = [
		'name_alert',
		'description',
		'threshold_km'
	];

	public function tbl_kilometers()
	{
		return $this->hasMany(TblKilometer::class, 'id_alert');
	}
}
