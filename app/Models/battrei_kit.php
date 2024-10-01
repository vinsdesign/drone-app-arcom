<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class battrei_kit extends Model
{
    use HasFactory;

    protected $fillable = [
        'kits_id',
        'battrei_id'
    ];
}
