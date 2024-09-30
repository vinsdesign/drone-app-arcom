<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fligh_personel extends Model
{
    use HasFactory;

    protected $fillable = [
        'pilot_id',
        'support_crew',
        'instructor_id',
        'other_id',
        'note'
    ];
}
