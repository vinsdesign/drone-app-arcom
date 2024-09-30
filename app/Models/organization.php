<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class organization extends Model
{
    use HasFactory;
    public function users(){
        return $this->hasMany(User::class);
    }
    protected $fillable = [
        'name',
        'email',
        'owner_name',
        'compani_size',
        'gov_registretion',
        'legalid',
        'exemption_number',
        'address',
        'state',
        'country',
        'insurance_amount',
        'activity',
        'image',
        'note'
    ];
}
