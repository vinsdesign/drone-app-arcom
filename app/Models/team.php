<?php

namespace App\Models;

use App\Filament\Resources\MaintenceResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Models\Contracts\HasAvatar;
use Storage;

class team extends Model implements HasAvatar
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
        'cities_id',
        'postal_code',
        'countries_id',
        'insurance',
        'insurance_amount',
        'activity',
        'note',
        'avatar_url',
        'customers_id',
        'projects_id',
        'set_pilot',
        'flight_type',
    ];

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null ;
    }
    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
    public function customers(){
        return $this->belongsToMany(Customer::class,);
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
    public function fligh_location(){
        return $this->belongsToMany(fligh_location::class);
    }
    public function countries(){
        return $this->belongsTo(countrie::class);
    }
    public function cities(){
        return $this->belongsTo(citie::class);
    }
    public function currencie(){
        return $this->belongsTo(currencie::class);
    }
    public function PlannedMission(){
        return $this->belongsToMany(PlannedMission::class);
    }
    public function getNameCustomer(){
        return $this->belongsTo(Customer::class, 'id_customers');
    }
    public function getNameProject(){
        return $this->belongsTo(Projects::class, 'id_projects');
    }


    public function getTotalFlightDurationAttribute()
    {
        $totalDurationInSeconds = $this->flighs->sum(function ($flight) {
            list($hours, $minutes, $seconds) = explode(':', $flight->duration);
            return ($hours * 3600) + ($minutes * 60) + $seconds;
        });

        $totalHours = floor($totalDurationInSeconds / 3600);
        $totalMinutes = floor(($totalDurationInSeconds % 3600) / 60);
        $totalSeconds = $totalDurationInSeconds % 60;

        // Format total durasi ke dalam HH:MM:SS
        return sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds);
    }
}
