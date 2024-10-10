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
        'teams_id'
    ];
   public function teams(){
    return $this->belongsTo(Team::class);
   }

   public function projects()
    {
        return $this->hasMany(Projects::class);
    }
   
}
