<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class drone_geometrie extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];
    public function drones(){
        return $this->hasMany(drone::class);
    }
}
