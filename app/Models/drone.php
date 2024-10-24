<?php

namespace App\Models;

use Database\Seeders\drone_geometries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'teams_id'
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
        return $this->belongsTo(Team::class);
    }
    public function kits()
    {
        return $this->belongsTo(kits::class);

    }
    public function fligh(){
        return $this->hasMany(fligh::class, 'drones_id');
    }

}
