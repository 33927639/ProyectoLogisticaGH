<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblDelivery
 *
 * @property int $id_delivery
 * @property Carbon $delivery_date
 * @property string $delivery_status
 * @property int $id_route
 * @property int $id_status
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property TblRoute $tbl_route
 * @property TblDeliveryStatus $tbl_delivery_status
 * @property Collection|TblDeliveryAssignment[] $tbl_delivery_assignments
 * @property Collection|TblDeliveryGuide[] $tbl_delivery_guides
 * @property Collection|TblIncome[] $tbl_incomes
 * @property Collection|TblKilometer[] $tbl_kilometers
 *
 * @package App\Models
 */
class TblDelivery extends Model
{
    protected $table = 'tbl_deliveries';
    protected $primaryKey = 'id_delivery';

    protected $casts = [
        'delivery_date' => 'datetime',
        'id_route'      => 'int',
        'id_status'     => 'int',
        'status'        => 'bool',
        'id_customer'   => 'int',      // <-- NUEVO
    ];

    protected $fillable = [
        'delivery_date',
        'delivery_status',
        'id_route',
        'id_status',
        'status',
        'id_customer',                 // <-- NUEVO
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\TblCustomer::class, 'id_customer', 'id_customer');
    }

    public function tbl_route()
	{
		return $this->belongsTo(TblRoute::class, 'id_route');
	}

	public function tbl_delivery_status()
	{
		return $this->belongsTo(TblDeliveryStatus::class, 'id_status');
	}

	public function tbl_delivery_assignments()
	{
		return $this->hasMany(TblDeliveryAssignment::class, 'id_delivery');
	}

	public function tbl_delivery_guides()
	{
		return $this->hasMany(TblDeliveryGuide::class, 'id_delivery');
	}

	public function tbl_incomes()
	{
		return $this->hasMany(TblIncome::class, 'id_delivery');
	}

	public function tbl_kilometers()
	{
		return $this->hasMany(TblKilometer::class, 'id_delivery');
	}
}
