<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class drone extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];

    public function battreis()
    {
        return $this->hasMany(Battrei::class, 'for_drone');
    }
}
