<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipality extends Model
{
    protected $table = 'municipalities';
    protected $primaryKey = 'id_municipality';

    protected $fillable = [
        'name_municipality',
        'department_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with department
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Relationship with customers
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'municipality_id');
    }

    /**
     * Relationship with routes as origin
     */
    public function originRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'origin_id');
    }

    /**
     * Relationship with routes as destination
     */
    public function destinationRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'destination_id');
    }
}
