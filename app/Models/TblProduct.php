<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TblProduct
 * 
 * @property int $id
 * @property string $name
 * @property int $stock
 * @property float|null $price
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models
 */
class TblProduct extends Model
{
	use SoftDeletes;
	protected $table = 'tbl_products';

	protected $casts = [
		'stock' => 'int',
		'price' => 'float',
		'status' => 'int'
	];

	protected $fillable = [
		'name',
		'stock',
		'price',
		'status'
	];
}
