<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * Campos asignables masivamente.
     */
    protected $fillable = [
        'name',
        'sku',
        'price',
        'stock',
        'description',
        'expires_at',
    ];

    /**
     * Conversión automática de tipos.
     */
    protected $casts = [
        'price'      => 'decimal:2',
        'stock'      => 'integer',
        'expires_at' => 'date',
    ];
}
