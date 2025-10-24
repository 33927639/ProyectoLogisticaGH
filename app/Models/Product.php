<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id_product';
    
    protected $fillable = [
        'name',
        'sku',
        'description',
        'unit_price',
        'weight_kg',
        'volume_m3',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'weight_kg' => 'decimal:2',
        'volume_m3' => 'decimal:3',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function deliveries(): BelongsToMany
    {
        return $this->belongsToMany(Delivery::class, 'delivery_products', 'product_id', 'delivery_id')
                    ->withPivot('quantity', 'unit_price', 'subtotal')
                    ->withTimestamps();
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'product_id', 'id_product');
    }
}
