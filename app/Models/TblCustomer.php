<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblCustomer
 * 
 * @property int $id_customer
 * @property string $name
 * @property string|null $nit
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property int|null $id_municipality
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property TblMunicipality|null $tbl_municipality
 *
 * @package App\Models
 */
class TblCustomer extends Model
{
	protected $table = 'tbl_customers';
	protected $primaryKey = 'id_customer';

	protected $casts = [
		'id_municipality' => 'int',
		'status' => 'bool'
	];

	protected $fillable = [
		'name',
		'nit',
		'phone',
		'email',
		'address',
		'id_municipality',
		'status'
	];

	public function tbl_municipality()
	{
		return $this->belongsTo(TblMunicipality::class, 'id_municipality');
	}
}
