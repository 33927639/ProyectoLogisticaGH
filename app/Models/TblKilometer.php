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
        'id_user'
    ];

    protected $casts = [
        'kilometers' => 'decimal:2',
        'date' => 'date',
        'id_vehicle' => 'integer',
        'id_user' => 'integer'
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'id_vehicle', 'id_vehicle');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
