<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TblKilometer extends Model
{
    protected $table = 'tbl_kilometers';
    protected $primaryKey = 'id_kilometer';
    
    protected $fillable = [
        'id_vehicle',
        'kilometers',
        'date',
        'notes',
        'id_user',
    ];

    protected $casts = [
        'date' => 'date',
        'kilometers' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with vehicle
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'id_vehicle', 'id_vehicle');
    }

    /**
     * Relationship with user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
