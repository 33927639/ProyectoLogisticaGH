<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblProduct extends Model
{
    //
    use SoftDeletes;

    protected $table = 'tbl_products';

    protected $casts = [
        'stock' => 'int',
        'price' => 'float',
        'status' => 'int',
    ];

    protected $fillable = [
        'name',
        'stock',
        'price',
        'status',
        'descripcion',
    ];
}
