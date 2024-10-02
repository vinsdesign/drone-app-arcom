<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class maintence_drone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'status',
        'cost',
        'currency',
        'notes',
        'drone_id',
        'part',
        'name_part',
        'status_part',
        'technician',
        'new_part_serial',
        'description_part'
    ];

    public function drone()
    {
        return $this->belongsTo(Drone::class, 'drone_id');
    }
}
