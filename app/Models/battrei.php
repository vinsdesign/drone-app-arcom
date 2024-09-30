<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'weight',
        'firmware_version',
        'hardware_version',
        'is_loaner',
        'description',
        'owner_id',
    ];

    public function drone()
    {
        return $this->belongsTo(Drone::class, 'for_drone');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}