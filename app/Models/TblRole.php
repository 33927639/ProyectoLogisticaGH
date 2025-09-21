<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblRole
 *
 * @property int $id_role
 * @property string $name_role
 * @property bool|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|TblRolesHasUser[] $tbl_roles_has_users
 *
 * @package App\Models
 */
class TblRole extends Model
{
    protected $table = 'tbl_roles';
    protected $primaryKey = 'id_role';

    protected $casts = [
        'status' => 'bool'
    ];

    protected $fillable = [
        'name_role',
        'status'
    ];

    public function tbl_roles_has_users()
    {
        return $this->hasMany(TblRolesHasUser::class, 'id_role');
    }
}
