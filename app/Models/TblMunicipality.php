<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblMunicipality
 * 
 * @property int $id_municipality
 * @property string $name_municipality
 * @property int $id_department
 * @property bool|null $status_municipality
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property TblDepartment $tbl_department
 * @property Collection|TblCustomer[] $tbl_customers
 * @property Collection|TblRoute[] $tbl_routes
 *
 * @package App\Models
 */
class TblMunicipality extends Model
{
	protected $table = 'tbl_municipalities';
	protected $primaryKey = 'id_municipality';

	protected $casts = [
		'id_department' => 'int',
		'status_municipality' => 'bool'
	];

	protected $fillable = [
		'name_municipality',
		'id_department',
		'status_municipality'
	];

	public function tbl_department()
	{
		return $this->belongsTo(TblDepartment::class, 'id_department');
	}

	public function tbl_customers()
	{
		return $this->hasMany(TblCustomer::class, 'id_municipality');
	}

	public function tbl_routes()
	{
		return $this->hasMany(TblRoute::class, 'id_origin');
	}
}
