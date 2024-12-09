<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fligh_location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'state',
        'country',
        'pos_code',
        'latitude',
        'longitude',
        'altitude',
        'teams_id',
        'customers_id',
        'projects_id',
        'status_visible',
        'users_id',
        'shared'
    ];
    public function teams(){
        return $this->belongsToMany(Team::class,'fligh_location_team','fligh_location_id','team_id');
       }
    public function Customers(){
        return $this->belongsTo(Customer::class);
    }
    public function Projects(){
        return $this->belongsTo(projects::class);
    }

    public function fligh()
    {
        return $this->hasMany(fligh::class, 'location_id');
    }
    public function PlannedMission()
    {
        return $this->hasMany(PlannedMission::class, 'location_id');
    }
    public function Incidents()
    {

        return $this->hasMany(Incident::class);

    }
    public function flights()
    {
        return $this->hasMany(fligh::class, 'location_id');
    }
}
