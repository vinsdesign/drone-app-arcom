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
        return $this->belongsTo(Project::class);
    }
    public function teams(){
        return $this->belongsTo(Team::class);
    }

}
