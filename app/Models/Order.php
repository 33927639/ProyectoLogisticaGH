<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id_order';
    public $timestamps = true;

    protected $fillable = [
        'customer_id',
        'order_date',
        'total_amount',
        'status'
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    // Relaciones
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id_customer');
    }

    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id_order');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'order_id', 'id_order');
    }

    // Métodos auxiliares
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'PENDING' => 'warning',
            'CONFIRMED' => 'info',
            'CANCELLED' => 'danger',
            'DELIVERED' => 'success',
            default => 'secondary'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'PENDING' => 'Pendiente',
            'CONFIRMED' => 'Confirmado',
            'CANCELLED' => 'Cancelado',
            'DELIVERED' => 'Entregado',
            default => 'Desconocido'
        };
    }

    public function calculateTotal(): float
    {
        return $this->products->sum('subtotal');
    }

    public function hasDeliveries(): bool
    {
        return $this->deliveries()->exists();
    }
}
