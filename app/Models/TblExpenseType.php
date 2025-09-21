<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblExpenseType
 *
 * @property int $id_expense_type
 * @property string $name
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|TblExpense[] $tbl_expenses
 *
 * @package App\Models
 */
class TblExpenseType extends Model
{
	protected $table = 'tbl_expense_types';
	protected $primaryKey = 'id_expense_type';

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'name',
		'status'
	];

	public function expense()
	{
		return $this->hasMany(TblExpense::class, 'id_expense_type');
	}
}
