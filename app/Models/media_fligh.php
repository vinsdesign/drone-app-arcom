<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class media_fligh extends Model
{
    protected $table = 'media_fligh';
    protected $fillable = [
        'id',
        'title',
        'description',
        'type',
        'url',
        'owner_id',
        'fligh_id'
    ];

    public function owners(){
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function flighs(){
        return $this->belongsTo(fligh::class, 'fligh_id');
    }
}
