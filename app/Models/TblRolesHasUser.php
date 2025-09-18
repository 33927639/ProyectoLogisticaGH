<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TblRolesHasUser
 * 
 * @property int $id_user
 * @property int $id_role
 * 
 * @property TblRole $tbl_role
 * @property TblUser $tbl_user
 *
 * @package App\Models
 */
class TblRolesHasUser extends Model
{
	protected $table = 'tbl_roles_has_users';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_user' => 'int',
		'id_role' => 'int'
	];

	public function tbl_role()
	{
		return $this->belongsTo(TblRole::class, 'id_role');
	}

	public function tbl_user()
	{
		return $this->belongsTo(TblUser::class, 'id_user');
	}
}
