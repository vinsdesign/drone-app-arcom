<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class document extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'refnumber',
        'expired_date',
        'scope',
        'external link',
        'description',
        'doc',
        'users_id',
        'customers_id',
        'projects_id',
        'teams_id',
        'status_visible',
        'shared',
        'locked',
        'battrei_id',
        'drone_id',
        'equidment_id'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function customers()
    {
        return $this->belongsTo(Customer::class);
    }
    public function projects()
    {
        return $this->belongsTo(Projects::class);
    }
    public function teams(){

        return $this->belongsToMany(Team::class, 'document_team','document_id', 'team_id');

    }
    public function drone()
    {
        return $this->belongsTo(drone::class, 'drone_id');
    }
    public function battrei()
    {
        return $this->belongsTo(battrei::class, 'battrei_id');
    }
    public function equidment()
    {
        return $this->belongsTo(equidment::class, 'equidment_id');
    }
    // public function scopeShared($query){
    //     return $query->where('shared',1);
    // }

}
