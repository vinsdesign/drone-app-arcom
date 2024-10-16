<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Projects extends Model
{
    use HasFactory;

    protected $fillable = [
        'case',
        'revenue',
        'currency',
        'customers_id',
        'description',
        'teams_id'
    ];

    public function customers(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function incident()
    {
        return $this->hasMany(Incident::class);
    }
    public function teams(){
        return $this->belongsTo(Team::class);
    }
    public function flight_locations()
    {
        return $this->hasMany(fligh_location::class);
    }

}
