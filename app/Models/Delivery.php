<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    // Le decimos a Laravel que use tu tabla real
    protected $table = 'tbl_deliveries';

    // Si tu tabla tiene primary key diferente a "id", descomenta y cambia esto:
    // protected $primaryKey = 'id_delivery';
}
