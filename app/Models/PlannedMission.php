<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlannedMission extends Model
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
        'kits_id',
        'drones_id',
        'battreis_id',
        'equidments_id',
        'pre_volt',
        'fuel_used',
        'status',
        'teams_id'
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

    public function drones(): BelongsTo
    {
        return $this->belongsTo(Drone::class);
    }

    public function battreis()
    {
        return $this->belongsToMany(battrei::class, 'planned_battery', 'planned_id', 'battrei_id');
    }

    public function equidments()
    {
        return $this->belongsToMany(Equidment::class, 'planned_equipment', 'planned_id', 'equidment_id');
    }
    public function teams(){
        return $this->belongsTo(team::class);
    }
    public function kits()
    {
        return $this->belongsTo(kits::class, 'kits_id');
    }
}
