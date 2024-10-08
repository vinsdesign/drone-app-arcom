<?php

namespace App\Models;

use App\Filament\Resources\MaintenceResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'owner',
        'website',
        'company_size',
        'gov_registration',
        'legal_id',
        'exemption_number',
        'address',
        'category',
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
    public function customers(){
        return $this->belongsToMany(Customer::class);
       }
    public function documents(){
        return $this->belongsToMany(Document::class);
       }
    public function flighs(){
        return $this->belongsToMany(Fligh::class);
    }
    public function incidents(){
        return $this->belongsToMany(Incident::class);
    }
    public function maintence_eqs(){
        return $this->belongsToMany(Maintence_eq::class);
    }
    public function maintence_drones(){
        return $this->belongsToMany(maintence_drone::class);
    }
    public function projects(){
        return $this->belongsToMany(Projects::class);
    }
    public function battreis(){
        return $this->belongsToMany(Battrei::class);
    }
    public function drones(){
        return $this->belongsToMany(Drone::class);
    }
    public function equidments(){
        return $this->belongsToMany(equidment::class);
    }
    public function kits(){
        return $this->belongsToMany(kits::class);
    }
    public function fligh_locations(){
        return $this->belongsToMany(Fligh_location::class);
    }
}
