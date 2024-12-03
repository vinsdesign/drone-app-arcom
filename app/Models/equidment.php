<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class equidment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model',
        'status',
        'inventory_asset',
        'serial',
        'type',
        'drones_id',
        'users_id',
        'purchase_date',
        'insurable_value',
        'weight',
        'is_loaner',
        'firmware_v',
        'hardware_v',
        'description',
        'teams_id',
        'users_id',
        'shared'

    ];

    public function drones(): BelongsTo
    {
        return $this->belongsTo(Drone::class);
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function maintence_eq()
    {
        return $this->hasMany(maintence_eq::class);
    }
    public function teams(){
        return $this->belongsToMany(Team::class,'equidment_team','equidment_id','team_id');
    }
    public function kits(){
        return $this->belongsToMany(kits::class, 'equidment_kits');
    }
    public function fligh(){
        return $this->belongsToMany(fligh::class, 'fligh_equidment', 'equidment_id', 'fligh_id');
    }
    public function PlannedMission(){
        return $this->belongsToMany(PlannedMission::class, 'planned_equipment', 'equidment_id', 'planned_id');
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
