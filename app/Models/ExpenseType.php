<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseType extends Model
{
    protected $table = 'expense_types';
    protected $primaryKey = 'id_expense_type';
    
    protected $fillable = [
        'name',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'expense_type_id');
    }
}
