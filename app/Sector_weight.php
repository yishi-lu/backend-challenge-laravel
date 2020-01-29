<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sector_weight extends Model
{
    protected $fillable = [
        'label', 'data', 'type'
    ];

    public function etf()
    {
        return $this->belongsTo(Etf::class);
    }
}
