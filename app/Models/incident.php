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
            'aircraft_damage',
            'other_damage',
            'description',
            'incuration_type',
            'rectification_note',
            'rectification_date',
            'Technician',
            'location_id',
            'drone_id',
            'project_id',
            'personel_involved_id'
    ];

    public function drone()
    {
        return $this->hasMany(drone::class);
    }
    public function project()
    {
        return $this->belongsTo(project::class);
    }
}
