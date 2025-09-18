<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblExpense
 * 
 * @property int $id_expense
 * @property int $id_expense_type
 * @property int $id_user
 * @property int|null $id_vehicle
 * @property string $description
 * @property float $amount
 * @property Carbon $expense_date
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property TblExpenseType $tbl_expense_type
 * @property TblUser $tbl_user
 * @property TblVehicle|null $tbl_vehicle
 *
 * @package App\Models
 */
class TblExpense extends Model
{
	protected $table = 'tbl_expenses';
	protected $primaryKey = 'id_expense';

	protected $casts = [
		'id_expense_type' => 'int',
		'id_user' => 'int',
		'id_vehicle' => 'int',
		'amount' => 'float',
		'expense_date' => 'datetime',
		'status' => 'bool'
	];

	protected $fillable = [
		'id_expense_type',
		'id_user',
		'id_vehicle',
		'description',
		'amount',
		'expense_date',
		'status'
	];

	public function tbl_expense_type()
	{
		return $this->belongsTo(TblExpenseType::class, 'id_expense_type');
	}

	public function tbl_user()
	{
		return $this->belongsTo(TblUser::class, 'id_user');
	}

	public function tbl_vehicle()
	{
		return $this->belongsTo(TblVehicle::class, 'id_vehicle');
	}
}
