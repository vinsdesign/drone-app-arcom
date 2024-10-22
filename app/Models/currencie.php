<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class currencie extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'iso',
        'id'
    ];
    public function projects(){
        return $this->hasMany(Projects::class);
    }
    public function maintenanceDrones(){
        return $this->hasMany(maintence_drone::class);
    }
    public function maintenanceEq(){
        return $this->hasMany(maintence_eq::class);
    }
}