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
        'description'

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
}
