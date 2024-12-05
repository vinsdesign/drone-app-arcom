<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'description',
        'teams_id',
        'status_visible'
    ];
   public function teams(){
    return $this->belongsTo(Team::class);
   }

   public function projects()
    {
        return $this->hasMany(Projects::class);
    }
    public function flight_locations()
    {
        return $this->hasMany(fligh_location::class);
    }
   
}
