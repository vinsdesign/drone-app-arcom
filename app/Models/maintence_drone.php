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
        'currencies_id',
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

    protected static function booted()
    {
        static::created(function ($maintence_drone) {
            $drone = drone::find($maintence_drone->drone_id);
    
            if ($drone && $drone->status === 'airworthy') {
                $drone->update(['status' => 'maintenance']);
            }
        });
        static::updated(function ($maintence_drone) {
            $drone = drone::find($maintence_drone->drone_id);

            if ($drone && $maintence_drone->status === 'completed' && $drone->status === 'maintenance') {
                $drone->update(['status' => 'airworthy']);
            }
        });
    }

    public function drone()
    {
        return $this->belongsTo(Drone::class, 'drone_id');
    }
    public function teams(){
        return $this->belongsTo(Team::class);
       }
       public function currencies(){
        return $this->belongsTo(currencie::class);
    }
}
