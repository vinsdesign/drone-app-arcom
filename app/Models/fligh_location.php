<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fligh_location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'state',
        'country',
        'pos_code',
        'latitude',
        'longitude',
        'altitude',
        'teams_id',
        'customer_id',
        'project_id',
    ];
    public function teams(){
        return $this->belongsTo(Team::class);
       }
    public function Customers(){
        return $this->belongsTo(Customer::class);
    }
    public function Projects(){
        return $this->belongsTo(project::class);
    }
    public function fligh()
    {
        return $this->hasMany(fligh::class);
    }
}
