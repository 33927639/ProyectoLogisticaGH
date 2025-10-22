<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'id_department';

    protected $fillable = [
        'name_department',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with municipalities
     */
    public function municipalities(): HasMany
    {
        return $this->hasMany(Municipality::class, 'department_id');
    }
}
