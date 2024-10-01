<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'inventory_id',
        'inventory_asset',
        'description',
        'users_id',
        'firmware_v',
        'hardware_v',
        'propulsion_v',
        'color',
        'remote',
        'conn_card'
    ];

    public function battreis()
    {
        return $this->hasMany(Battrei::class, 'for_drone');
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function incident()
    {
        return $this->belongsTo(incident::class);
    }

}
