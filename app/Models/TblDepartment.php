<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblDepartment
 * 
 * @property int $id_department
 * @property string $name_department
 * @property bool|null $status_department
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|TblMunicipality[] $tbl_municipalities
 *
 * @package App\Models
 */
class TblDepartment extends Model
{
	protected $table = 'tbl_departments';
	protected $primaryKey = 'id_department';

	protected $casts = [
		'status_department' => 'bool'
	];

	protected $fillable = [
		'name_department',
		'status_department'
	];

	public function tbl_municipalities()
	{
		return $this->hasMany(TblMunicipality::class, 'id_department');
	}
}
