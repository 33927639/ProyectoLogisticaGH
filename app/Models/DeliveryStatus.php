<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryStatus extends Model
{
    protected $table = 'delivery_statuses';
    protected $primaryKey = 'id_status';
    
    protected $fillable = [
        'name_status',
        'description',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'status_id');
    }
}
