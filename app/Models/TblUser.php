<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblUser
 * 
 * @property int $id_user
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string|null $jwt_token
 * @property Carbon|null $jwt_expiration
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|TblExpense[] $tbl_expenses
 * @property Collection|TblIncome[] $tbl_incomes
 * @property Collection|TblMaintenanceRequest[] $tbl_maintenance_requests
 * @property Collection|TblRolesHasUser[] $tbl_roles_has_users
 *
 * @package App\Models
 */
class TblUser extends Model
{
	protected $table = 'tbl_users';
	protected $primaryKey = 'id_user';

	protected $casts = [
		'jwt_expiration' => 'datetime',
		'status' => 'bool'
	];

	protected $hidden = [
		'password',
		'jwt_token'
	];

	protected $fillable = [
		'first_name',
		'last_name',
		'username',
		'email',
		'password',
		'jwt_token',
		'jwt_expiration',
		'status'
	];

	public function tbl_expenses()
	{
		return $this->hasMany(TblExpense::class, 'id_user');
	}

	public function tbl_incomes()
	{
		return $this->hasMany(TblIncome::class, 'id_user');
	}

	public function tbl_maintenance_requests()
	{
		return $this->hasMany(TblMaintenanceRequest::class, 'approved_by');
	}

	public function tbl_roles_has_users()
	{
		return $this->hasMany(TblRolesHasUser::class, 'id_user');
	}
}
