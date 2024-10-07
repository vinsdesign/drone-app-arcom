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
        'replaced',
        'part_name',
        'status_part',
        'technician',
        'new_part_serial',
        'description_part',
        'teams_id'
    ];

    public function drone()
    {
        return $this->belongsTo(Drone::class, 'drone_id');
    }
    public function teams(){
        return $this->belongsTo(Team::class);
       }
}
