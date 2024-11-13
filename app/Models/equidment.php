<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
