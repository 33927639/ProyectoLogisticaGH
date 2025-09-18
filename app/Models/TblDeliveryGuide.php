<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblDeliveryGuide
 * 
 * @property int $id_guide
 * @property int $id_delivery
 * @property string $guide_number
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property TblDelivery $tbl_delivery
 *
 * @package App\Models
 */
class TblDeliveryGuide extends Model
{
	protected $table = 'tbl_delivery_guides';
	protected $primaryKey = 'id_guide';

	protected $casts = [
		'id_delivery' => 'int',
		'status' => 'bool'
	];

	protected $fillable = [
		'id_delivery',
		'guide_number',
		'status'
	];

	public function tbl_delivery()
	{
		return $this->belongsTo(TblDelivery::class, 'id_delivery');
	}
}
