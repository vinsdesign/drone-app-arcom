<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class maintence_eq extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'status',
        'cost',
        'currency',
        'notes',
        'equidment_id',
        'teams_id'
    ];

    public function equidment()
    {
        return $this->belongsTo(equidment::class);
    }
    public function teams(){
        return $this->belongsTo(Team::class);
    }
}
