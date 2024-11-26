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
        'projects_id',
        'personel_involved_id',
        'aircraft_damage',
        'other_damage',
        'description',
        'incuration_type',
        'rectification_note',
        'rectification_date',
        'teams_id',
        'status',
        'Technician',
        'status'
    ];

    protected static function booted()
    {
        static::created(function ($incident) {
            $drone = drone::find($incident->drone_id);
            if ($drone && $drone->status === 'airworthy') {
                $drone->update(['status' => 'maintenance']);
            }
        });
    }

    public function drone()
    {
        return $this->belongsTo(Drone::class, 'drone_id');
    }

    public function project()
    {
        return $this->belongsTo(projects::class, 'projects_id');
    }
    public function teams(){
        return $this->belongsTo(Team::class);
       }
    public function fligh_locations(){
        return $this->belongsTo(fligh_location::class,'location_id');
    }
    public function users(){
        return $this->belongsTo(User::class, 'personel_involved_id');
    }
}
