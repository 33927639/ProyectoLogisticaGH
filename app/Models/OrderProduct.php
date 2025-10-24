<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProduct extends Model
{
    protected $table = 'order_products';
    protected $primaryKey = 'id_order_product';
    public $timestamps = true;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_description',
        'quantity',
        'unit',
        'unit_price',
        'subtotal'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relaciones
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id_order');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id_product');
    }

    // Event listeners para calcular automáticamente el subtotal
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($orderProduct) {
            // Si se seleccionó un producto y no se ha establecido nombre/precio manualmente
            if ($orderProduct->product_id && !$orderProduct->product_name) {
                $product = Product::find($orderProduct->product_id);
                if ($product) {
                    $orderProduct->product_name = $product->name;
                    $orderProduct->product_description = $product->description;
                    $orderProduct->unit_price = $product->unit_price;
                }
            }
            
            // Calcular subtotal
            $orderProduct->subtotal = $orderProduct->quantity * $orderProduct->unit_price;
        });

        static::saved(function ($orderProduct) {
            // Actualizar el total de la orden cuando se guarde un producto
            if ($orderProduct->order) {
                $total = $orderProduct->order->products->sum('subtotal');
                $orderProduct->order->update(['total_amount' => $total]);
            }
        });

        static::deleted(function ($orderProduct) {
            // Actualizar el total de la orden cuando se elimine un producto
            if ($orderProduct->order) {
                $total = $orderProduct->order->products->sum('subtotal');
                $orderProduct->order->update(['total_amount' => $total]);
            }
        });
    }

    // Métodos auxiliares
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Q ' . number_format($this->subtotal, 2);
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return 'Q ' . number_format($this->unit_price, 2);
    }

    public function getFormattedQuantityAttribute(): string
    {
        return number_format($this->quantity, 2) . ' ' . $this->unit;
    }
}