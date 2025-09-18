<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblIncome
 * 
 * @property int $id_income
 * @property float|null $amount
 * @property string $description
 * @property Carbon $income_date
 * @property int $id_user
 * @property int|null $id_delivery
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property TblDelivery|null $tbl_delivery
 * @property TblUser $tbl_user
 *
 * @package App\Models
 */
class TblIncome extends Model
{
	protected $table = 'tbl_incomes';
	protected $primaryKey = 'id_income';

	protected $casts = [
		'amount' => 'float',
		'income_date' => 'datetime',
		'id_user' => 'int',
		'id_delivery' => 'int',
		'status' => 'bool'
	];

	protected $fillable = [
		'amount',
		'description',
		'income_date',
		'id_user',
		'id_delivery',
		'status'
	];

	public function tbl_delivery()
	{
		return $this->belongsTo(TblDelivery::class, 'id_delivery');
	}

	public function tbl_user()
	{
		return $this->belongsTo(TblUser::class, 'id_user');
	}
}
