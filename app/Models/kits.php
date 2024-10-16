<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kits extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
         'enabled',
        'drone_id',
        'teams_id'
    ];
    public function teams(){
        return $this->belongsTo(Team::class);
    }
    public function drone()
    {
        return $this->belongsTo(drone::class);
    }
    public function battrei(){
        return $this->belongsToMany(battrei::class);
    }
    public function equidment(){
        return $this->belongsToMany(equidment::class);
    }
}
