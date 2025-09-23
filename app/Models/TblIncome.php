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
 * @property int $id_customer
 * @property int|null $id_delivery
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property TblDelivery|null $tbl_delivery
 * @property TblCustomer $tbl_customer
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
		'id_customer' => 'int',
		'id_delivery' => 'int',
		'status' => 'bool'
	];

	protected $fillable = [
		'amount',
		'description',
		'income_date',
		'id_customer',
		'id_delivery',
		'status'
	];

	public function tbl_delivery()
	{
		return $this->belongsTo(TblDelivery::class, 'id_delivery');
	}

	public function tbl_customer()
	{
		return $this->belongsTo(TblCustomer::class, 'id_customer');
	}

    public function customer()
    {
        return $this->belongsTo(TblCustomer::class, 'id_customer');
    }
}
