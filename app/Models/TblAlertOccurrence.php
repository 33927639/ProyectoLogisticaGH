<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAlertOccurrence extends Model
{
    protected $table = 'tbl_alert_occurrences';
    protected $primaryKey = 'id_occurrence';

    protected $fillable = [
        'id_alert',
        'message',
        'triggered_at',
    ];

    public function alert()
    {
        return $this->belongsTo(TblAlertStatus::class, 'id_alert');
    }
}
