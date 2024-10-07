<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class organization extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'owner',
        'website',
        'company_size',
        'gov_registretion',
        'legal_id',
        'exemption_number',
        'address',
        'state',
        'city',
        'postal_code',
        'country',
        'insurance',
        'insurance_amount',
        'activity',
        'note'
    ];
    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
