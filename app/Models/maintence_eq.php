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
        'currency',
        'notes',
        'equidment_id',
        'teams_id'
    ];

    protected static function booted()
    {
        static::created(function ($maintence_eq) {
            $equipment = equidment::find($maintence_eq->equidment_id);
    
            if ($equipment && $equipment->status === 'airworthy') {
                $equipment->update(['status' => 'maintenance']);
            }
        });
    
        // Event saat maintenance diupdate
        static::updated(function ($maintence_eq) {
            $equipment = equidment::find($maintence_eq->equidment_id);
    
            // Cek jika maintenance sudah selesai
            if ($maintence_eq->status === 'completed' && $equipment && $equipment->status === 'maintenance') {
                $equipment->update(['status' => 'airworthy']);
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
}
