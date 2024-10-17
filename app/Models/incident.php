<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_date',
        'cause',
        'location_id',
        'drone_id',
        'project_id',
        'personel_involved_id',
        'aircraft_damage',
        'other_damage',
        'description',
        'incuration_type',
        'rectification_note',
        'rectification_date',
        'teams_id',
        'Technician',
    ];

    public function drone()
    {
        return $this->belongsTo(Drone::class, 'drone_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function teams(){
        return $this->belongsTo(Team::class);
       }
    public function fligh_locations(){
        return $this->belongsTo(fligh_location::class,'location_id');
    }
}
