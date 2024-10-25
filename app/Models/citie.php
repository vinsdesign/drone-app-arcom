<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class citie extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'country_id'
    ];
    public function users(){
        return $this->hasMany(User::class);
    }
    public function teams(){
        return $this->hasMany(team::class);
    }
}