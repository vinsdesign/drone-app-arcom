<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatteryCharger extends Model
{
    protected $table = 'charger_batteries';
    protected $fillable = [
        'date',
        'duration',
        'note',
        'pre_flight',
        'post_flight',
        'before_charger',
        'after_charger',
        'capacity',
        'resistance',
        'cell1',
        'cell2',
        'cell3',
        'cell4',
        'cell5',
        'cell6',
        'cell7',
        'cell8',
        'batteris_id',
    ];

    public function battery()
    {
        return $this->belongsTo(battrei::class, 'batteris_id');
    }
}
