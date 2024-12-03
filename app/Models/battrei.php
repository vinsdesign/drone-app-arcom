<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class battrei extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'model',
        'status',
        'asset_inventory',
        'serial_P',
        'serial_I',
        'cellCount',
        'nominal_voltage',
        'capacity',
        'initial_Cycle_count',
        'life_span',
        'flaight_count',
        'for_drone',
        'purchase_date',
        'insurable_value',
        'wight',
        'firmware_version',
        'hardware_version',
        'is_loaner',
        'description',
        'users_id',
        'teams_id',
        'users_id',
        'shared'
    ];

    public function drone()
    {
        return $this->belongsTo(Drone::class, 'for_drone');
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function teams(){
        return $this->belongsToMany(Team::class,'battrei_team','battrei_id','team_id');
    }
    public function kits(){
        return $this->belongsToMany(kits::class, 'battrei_kits');
    }
    public function fligh(){
        return $this->belongsToMany(fligh::class, 'fligh_battrei', 'battrei_id', 'fligh_id');
    }
    public function PlannedMission(){
        return $this->belongsToMany(PlannedMission::class, 'planned_battery', 'battrei_id', 'planned_id');
    }
    public function maintence_eq()
    {
        return $this->hasMany(maintence_eq::class);
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
}