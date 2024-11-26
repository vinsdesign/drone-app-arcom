<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class maintence_eq extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'status',
        'cost',
        'currencies_id',
        'notes',
        'equidment_id',
        'battrei_id',
        'teams_id',
        'technician',
    ];

    protected static function booted()
    {
        static::created(function ($maintence_eq) {
            $equipment = equidment::find($maintence_eq->equidment_id);
    
            if ($equipment && $equipment->status === 'airworthy') {
                $equipment->update(['status' => 'maintenance']);
            }
        });

        static::created(function ($maintence_eq) {
            $battery = battrei::find($maintence_eq->battrei_id);
    
            if ($battery && $battery->status === 'airworthy') {
                $battery->update(['status' => 'maintenance']);
            }
        });
    
        static::updated(function ($maintence_eq) {
            $equipment = equidment::find($maintence_eq->equidment_id);
    
            if ($maintence_eq->status === 'completed' && $equipment && $equipment->status === 'maintenance') {
                $equipment->update(['status' => 'airworthy']);
            }
        });

        static::updated(function ($maintence_eq) {
            $battery = battrei::find($maintence_eq->battrei_id);
    
            if ($maintence_eq->status === 'completed' && $battery && $battery->status === 'maintenance') {
                $battery->update(['status' => 'airworthy']);
            }
        });
    }

    public function equidment()
    {
        return $this->belongsTo(equidment::class);
    }
    public function teams(){
        return $this->belongsTo(Team::class);
    }
    public function currencies(){
        return $this->belongsTo(currencie::class);
    }
    public function battrei()
    {
        return $this->belongsTo(battrei::class, 'battrei_id');
    }
}
