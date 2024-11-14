<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class fligh extends Model
{
    use HasFactory;

    protected $fillable =[
       'name',
        'start_date_flight',
        'end_date_flight',
        'duration',
        'type',
        'ops',
        'landings',
        'customers_id',
        'location_id',
        'projects_id',
        'users_id',
        'vo',
        'po',
        'kits_id',
        'instructor',
        'drones_id',
        'battreis_id',
        'equidments_id',
        'pre_volt',
        'fuel_used',
        'teams_id',
        'shared',
        'locked_flight'
    ];

    public function customers(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function projects(): BelongsTo
    {
        return $this->belongsTo(Projects::class);
    }

    public function fligh_location(): BelongsTo
    {
        return $this->belongsTo(fligh_location::class, 'location_id', 'id');
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function instructors(): BelongsTo
    {
        return $this->belongsTo(User::class,'instructor', 'id');
    }

    public function drones(): BelongsTo
    {
        return $this->belongsTo(Drone::class);
    }

    public function battreis()
    {
        return $this->belongsToMany(battrei::class, 'fligh_battrei', 'fligh_id', 'battrei_id');
    }

    public function equidments()
    {
        return $this->belongsToMany(Equidment::class, 'fligh_equidment', 'fligh_id', 'equidment_id');
    }
    public function teams(){
        return $this->belongsToMany(Team::class, 'fligh_team',  'fligh_id','team_id');
    }
    public function kits()
    {
        return $this->belongsTo(kits::class, 'kits_id');
    }
}
