<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'products';


    protected $fillable =
        [
        'name',
        'stock',
        'price',
        'description',
        'sku',
        'status'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
        'id' => 'integer',
        'name' => 'string',
        'stock' => 'string',
        'price' => 'string',
        'description' => 'string',
        'sku' => 'string',
        'status' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];



    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules =
    [
        'name' => 'required',
        'stock' => 'required',
        'price' => 'required',
        'description' => 'required',
        'sku' => 'required',
        'status' => 'required',
    ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages =[

    ];


    /**
     * Accessor for relationships
     *
     * @var array
     */
    

}
