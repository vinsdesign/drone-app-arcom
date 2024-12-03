<?php

namespace App\Models;

use Database\Seeders\drone_geometries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class drone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'idlegal',
        'brand',
        'model',
        'type',
        'serial_p',
        'serial_i',
        'flight_c',
        'remote_c',
        'remote_cc',
        'geometry',
        'inventory_asset',
        'description',
        'users_id',
        'firmware_v',
        'hardware_v',
        'propulsion_v',
        'color',
        'remote',
        'conn_card',
        'initial_flight',
        'initial_flight_time',
        'max_flight_time',
        'teams_id',
        'shared'
    ];

    public function batteries()
    {
        return $this->hasMany(battrei::class, 'for_drone');
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
    public function maintence_drone()
    {
        return $this->hasMany(maintence_drone::class);
    }

    public function teams(){
        return $this->belongsToMany(Team::class,'drone_team','drone_id','team_id');
    }
    public function kits()
    {
        return $this->belongsTo(kits::class);

    }
    public function fligh(){
        return $this->hasMany(fligh::class, 'drones_id');
    }


    //edit query untuk action shared un-shared
    public function scopeAccessibleBy(Builder $query, $user)
    {
        if ($user->roles()->pluck('name')->contains('super_admin') || $user->roles()->pluck('name')->contains('panel_user')) {
            return $query;
        }

        $userId = $user->id;

        return $query->where(function ($query) use ($userId) {
            $query->where('users_id', $userId);
        })
        ->orWhere(function ($query) use ($userId) {
            $query->where('users_id', '!=', $userId)->where('shared', 1);
        });
    }
    
    public function getTotalFlyingTimeAttribute()
    {
        $totalSeconds = 0;
        foreach ($this->fligh as $flight) {
            if (preg_match('/^(\d+):(\d{2}):(\d{2})$/', trim($flight->duration), $matches)) {
                list(, $hours, $minutes, $seconds) = $matches;
                $totalSeconds += ($hours * 3600) + ($minutes * 60) + (int)$seconds;
            }
        }
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

}
