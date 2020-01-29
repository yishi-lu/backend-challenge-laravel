<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Etf_info extends Model
{
    protected $fillable = [
        'key_feature', 
        'about', 
        'primary_benchmark', 
        'secondary_benchmark', 
        'inception', 
        'options', 
        'gross_expense_ratio', 
        'base_currency', 
        'investment_manager', 
        'management_team', 
        'sub_advisor', 
        'distributor', 
        'distribution_frequency', 
        'trustee', 
        'marketing_agent', 
        'gold_custodian', 
        'sponsor', 
        'exchange', 
        'listing_date', 
        'trading_currency', 
        'CUSIP',
        'ISIN', 
    ];

    public function etf()
    {
        return $this->belongsTo(Etf::class);
    }
}
