<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Delivery extends Model
{
    protected $table = 'deliveries';
    protected $primaryKey = 'id_delivery';
    
    protected $fillable = [
        'order_id',
        'customer_name',
        'delivery_address',
        'total_amount',
        'delivery_date',
        'route_id',
        'status_id',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'total_amount' => 'decimal:2',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id_order');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function deliveryStatus(): BelongsTo
    {
        return $this->belongsTo(DeliveryStatus::class, 'status_id');
    }

    public function deliveryAssignments(): HasMany
    {
        return $this->hasMany(DeliveryAssignment::class, 'delivery_id');
    }

    /**
     * Get vehicles assigned to this delivery
     */
    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'delivery_assignments', 'delivery_id', 'vehicle_id')
                    ->withPivot('driver_id', 'assignment_date', 'status')
                    ->withTimestamps();
    }

    /**
     * Get drivers assigned to this delivery
     */
    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class, 'delivery_assignments', 'delivery_id', 'driver_id')
                    ->withPivot('vehicle_id', 'assignment_date', 'status')
                    ->withTimestamps();
    }

    public function deliveryGuides(): HasMany
    {
        return $this->hasMany(DeliveryGuide::class, 'delivery_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'delivery_products', 'delivery_id', 'product_id')
                    ->withPivot('quantity', 'unit_price', 'subtotal')
                    ->withTimestamps();
    }

    public function deliveryLogs(): HasMany
    {
        return $this->hasMany(DeliveryLog::class, 'delivery_id');
    }

    public function kilometers(): HasMany
    {
        return $this->hasMany(Kilometer::class, 'delivery_id');
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class, 'delivery_id');
    }

    public function deliveryPayment(): HasMany
    {
        return $this->hasMany(DeliveryPayment::class, 'delivery_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'delivery_id');
    }
}
