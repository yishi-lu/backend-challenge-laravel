<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Etf extends Model
{
    protected $fillable = [
        'fundName', 'fundTicker', 'fundUri', 'ter', 'nav', 'aum', 'asOfDate', 'fundFilter', 'domicile'
    ];

    public function topHolding()
    {
        return $this->hasMany(Top_holding::class)->orderBy('weight', 'DESC');;
    }

    public function sectorWeight()
    {
        return $this->hasMany(Sector_weight::class)->orderBy('data', 'DESC');;
    }

    public function countryWeight()
    {
        return $this->hasMany(Country_weight::class)->orderBy('weight', 'DESC');;
    }

    public function etfInfo()
    {
        return $this->hasMany(Etf_info::class);
    }
}
