<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class wheater extends Model
{
    use HasFactory;

    protected $fillable = [
       'temperature',
           'humidity',
            'note',
           'wind_speed',
           'pressure',
           'cloud_cover',
           'visibility'
    ];
}
