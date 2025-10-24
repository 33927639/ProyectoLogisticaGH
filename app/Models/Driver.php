<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $table = 'drivers';
    protected $primaryKey = 'id_driver';
    
    protected $fillable = [
        'name',
        'license',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getFirstNameAttribute(): string
    {
        $parts = explode(' ', $this->name);
        return $parts[0] ?? '';
    }

    public function deliveryAssignments(): HasMany
    {
        return $this->hasMany(DeliveryAssignment::class, 'driver_id');
    }
}
