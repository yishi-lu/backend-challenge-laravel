<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Top_holding extends Model
{
    protected $fillable = [
        'name', 'fundTicker', 'market_value', 'per_value', 'total_mkt_cap_m', 'ISIN', 'shares', 'ticker', 'weight', 'type'
    ];

    public function etf()
    {
        return $this->belongsTo(Etf::class);
    }
}
