<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'for_drone',
        'owner_id',
        'purchase_date',
        'insurable_value',
        'firmware_v',
        'hardware_v',
        'description'

    ];
}
