<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country_weight extends Model
{
    protected $fillable = [
        'name', 'weight', 
    ];

    public function etf()
    {
        return $this->belongsTo(Etf::class);
    }
}
