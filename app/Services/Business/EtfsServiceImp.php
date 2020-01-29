<?php

namespace App\Services\Business;

use App\Contracts\Business\EtfsService;
use App\Contracts\Business\ParserService;
use App\Etf; 
use App\Etf_info; 

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * EtfsServiceImp
 *
 * Created by Yishi Lu.
 * User: Yishi Lu
 * Date: 2020/01/27
 */
class EtfsServiceImp implements EtfsService{

    private $parser;

    public function __construct(ParserService $parser){
        $this->parser = $parser;
    }

    /**
     * fetch all etf symbols 
     *
     * @param null
     * @return array
     */
    function getAllEtfs(){

        $etfs = Etf::all();

        foreach($etfs as $etf){

            $etf['fundUri'] = "http://69.203.92.72:8080/api/etfs/etfDetail/".$etf['fundTicker'];
        }

        return $etfs;
    }
    
    /**
     * fetch etf symbol with given ticker
     *
     * @param ticker
     * @return array
     */
    function getEtfByTicker($ticker){

        $result = array();
        
        $etfSymbol = Etf::where('fundTicker', '=', $ticker)->first();

        if($etfSymbol == null) return null;

        $result['fund_top_holding'] = $etfSymbol
                                    ->topHolding()
                                    ->where('type', '=', 'fund')
                                    ->whereDate('updated_at', Carbon::today())
                                    ->get();

        $result['fund_top_holding'] = $etfSymbol
                        ->topHolding()
                        ->where('type', '=', 'fund')
                        ->whereDate('updated_at', Carbon::today())
                        ->get();

        $result['index_top_holding'] = $etfSymbol
                        ->topHolding()
                        ->where('type', '=', 'index')
                        ->whereDate('updated_at', Carbon::today())
                        ->get();

        $result['fund_sector_weight'] = $etfSymbol
                        ->sectorWeight()
                        ->where('type', '=', 'fund')
                        ->whereDate('updated_at', Carbon::today())
                        ->get();

        $result['index_sector_weight'] = $etfSymbol
                        ->sectorWeight()
                        ->where('type', '=', 'index')
                        ->whereDate('updated_at', Carbon::today())
                        ->get();

        $result['country_weight'] = $etfSymbol
                        ->countryWeight()
                        ->whereDate('updated_at', Carbon::today())
                        ->get();

        $result['etf_info'] = $etfSymbol
                        ->etfInfo()
                        ->whereDate('updated_at', Carbon::today())
                        ->get();
        
        return $result;


    }

    /**
     * update etf symbols from given url
     *
     * @param url
     * @return null;
     */
    function updateEtfs($url){

        $result = $this->parser->parseEtfs($url);

        $etfItems = $result->data->us->funds->etfs->overview->datas;

        foreach($etfItems as $item){

            $domicile = $item->domicile;
            $fundName = $item->fundName;
            $fundTicker = $item->fundTicker;
            $fundUri = $item->fundUri;
            $ter = $item->ter;
            $nav = $item->nav;
            $aum = $item->aum;
            $asOfDate = $item->asOfDate[1];
            $fundFilter = $item->fundFilter;

            $etfSymbol = Etf::where('fundTicker', '=', $fundTicker)->first();

            if($etfSymbol != null) {
                $etfSymbol->domicile = $domicile;
                $etfSymbol->fundName = $fundName;
                $etfSymbol->fundTicker = $fundTicker;
                $etfSymbol->fundUri = $fundUri;
                $etfSymbol->ter = $ter;
                $etfSymbol->nav = $nav;
                $etfSymbol->aum = $aum;
                $etfSymbol->asOfDate = $asOfDate;
                $etfSymbol->fundFilter = $fundFilter;

                $etfSymbol->save();
            }
            else {

                $etfSymbol = Etf::create(['domicile' => $domicile, 
                                        'fundName' => $fundName, 
                                        'fundTicker' => $fundTicker, 
                                        'fundUri' => $fundUri, 
                                        'ter' => $ter, 
                                        'nav' => $nav, 
                                        'aum' => $aum,
                                        'asOfDate' => $asOfDate, 
                                        'fundFilter' => $fundFilter]);

            }

            $this->updateEtfsAttributes($etfSymbol, $fundUri);

        }
    }

    /**
     * update detail information of etfs by given etf id and url
     *
     * @param id, url
     * @return null
     */
    function updateEtfsAttributes($etfSymbol, $url){

        $targetURL = env('TARGET_DOMAIN').$url;
        $result = $this->parser->parseEtfDetail($targetURL);

        if(is_array($result['fund_top_holdings'])){
            foreach($result['fund_top_holdings'] as $item){

                $etfSymbol->topHolding()->create([
                    'name' => $item['name'],
                    'market_value' => $item['market_value'] ?? null,
                    'par_value' => $item['par_value'] ?? null,
                    'total_mkt_cap_m' => $item['total_mkt_cap_m'] ?? null,
                    'ISIN' => $item['ISIN'] ?? null,
                    'shares' => $item['shares'] ?? null,
                    'ticker' => $item['ticker'] ?? null,
                    'weight' => $item['weight'] ?? null,
                    'type' => 'fund' ?? null,
                ]);
            }
        }

        if(is_array($result['index_top_holdings'])){
            foreach($result['index_top_holdings'] as $item){

                $etfSymbol->topHolding()->create([
                    'name' => $item['name'] ?? null,
                    'market_value' => $item['market_value'] ?? null,
                    'par_value' => $item['par_value'] ?? null,
                    'total_mkt_cap_m' => $item['total_mkt_cap_m'] ?? null,
                    'ISIN' => $item['ISIN'] ?? null,
                    'shares' => $item['shares'] ?? null,
                    'ticker' => $item['ticker'] ?? null,
                    'weight' => $item['weight'] ?? null,
                    'type' => 'index' ?? null,
                ]);
            }
        }

        if(is_array($result['fund_sector_weight'])){
            foreach($result['fund_sector_weight'] as $item){

                $etfSymbol->sectorWeight()->create([
                    'label' => $item['label'] ?? null,
                    'data' => $item['data'] ?? null,
                    'type' => 'fund' ?? null,
                ]);
            }
        }

        if(is_array($result['index_sector_weight'])){
            foreach($result['index_sector_weight'] as $item){

                $etfSymbol->sectorWeight()->create([
                    'label' => $item['label'] ?? null,
                    'data' => $item['data'] ?? null,
                    'type' => 'index' ?? null,
                ]);
            }
        }

        if(is_array($result['country_weight'])){
            foreach($result['country_weight'] as $item){

                $etfSymbol->countryWeight()->create([
                    'name' => $item->name->value ?? null,
                    'weight' => $item->weight->value ?? null,
                ]);

            }
        }

        $primaryBenchmark = $result['eft_info']['fund_info']['Benchmark'] ?? null; 
        $primaryBenchmark = $result['eft_info']['fund_info']['Primary Benchmark'] ?? $primaryBenchmark;

        $etfSymbolInfo = Etf_info::where('etf_id', '=', $etfSymbol->id)->first();

        if($etfSymbolInfo == null){
            $etfSymbol->etfInfo()->create([
                'key_feature' => $result['eft_info']['key_feature'] ?? null,
                'about' => $result['eft_info']['about'] ?? null,
                'primary_benchmark' => $primaryBenchmark ?? null,
                'secondary_benchmark' => $result['eft_info']['fund_info']['Secondary Benchmark'] ?? null,
                'inception' => $result['eft_info']['fund_info']['Inception Date'] ?? null,
                'options' => $result['eft_info']['fund_info']['Options Available'] ?? null,
                'gross_expense_ratio' => $result['eft_info']['fund_info']['Gross Expense Ratio'] ?? null,
                'base_currency' => $result['eft_info']['fund_info']['Base Currency'] ?? null,
                'investment_manager' => $result['eft_info']['fund_info']['Investment Manager'] ?? null,
                'management_team' => $result['eft_info']['fund_info']['Management Team'] ?? null,
                'sub_advisor' => $result['eft_info']['fund_info']['Sub-advisor'] ?? null,
                'distributor' => $result['eft_info']['fund_info']['Distributor'] ?? null,
                'distribution_frequency' => $result['eft_info']['fund_info']['Distribution Frequency'] ?? null,
                'trustee' => $result['eft_info']['fund_info']['Trustee'] ?? null,
                'marketing_agent' => $result['eft_info']['fund_info']['Marketing Agent'] ?? null,
                'gold_custodian' => $result['eft_info']['fund_info']['Gold Custodian'] ?? null,
                'sponsor' => $result['eft_info']['fund_info']['Sponsor'] ?? null,
                'exchange' => $result['eft_info']['listing_info']['Exchange'] ?? null,
                'listing_date' => $result['eft_info']['listing_info']['Listing Date'] ?? null,
                'trading_currency' => $result['eft_info']['listing_info']['Trading Currency'] ?? null,
                'CUSIP' => $result['eft_info']['listing_info']['CUSIP'] ?? null,
                'ISIN' => $result['eft_info']['listing_info']['ISIN'] ?? null,
            ]);
        }
        else {
            $etfSymbolInfo->key_feature = $etfSymbolInfo->id;
            $etfSymbolInfo->about = $result['eft_info']['about'] ?? null;
            $etfSymbolInfo->primary_benchmark = $primaryBenchmark ?? null;
            $etfSymbolInfo->secondary_benchmark = $result['eft_info']['fund_info']['Secondary Benchmark'] ?? null;
            $etfSymbolInfo->inception = $result['eft_info']['fund_info']['Inception Date'] ?? null;
            $etfSymbolInfo->options = $result['eft_info']['fund_info']['Options Available'] ?? null;
            $etfSymbolInfo->gross_expense_ratio = $result['eft_info']['fund_info']['Gross Expense Ratio'] ?? null;
            $etfSymbolInfo->base_currency = $result['eft_info']['fund_info']['Base Currency'] ?? null;
            $etfSymbolInfo->investment_manager = $result['eft_info']['fund_info']['Investment Manager'] ?? null;
            $etfSymbolInfo->management_team = $result['eft_info']['fund_info']['Management Team'] ?? null;
            $etfSymbolInfo->sub_advisor = $result['eft_info']['fund_info']['Sub-advisor'] ?? null;
            $etfSymbolInfo->distributor = $result['eft_info']['fund_info']['Distributor'] ?? null;
            $etfSymbolInfo->distribution_frequency =  $result['eft_info']['fund_info']['Distribution Frequency'] ?? null;
            $etfSymbolInfo->trustee = $result['eft_info']['fund_info']['Trustee'] ?? null;
            $etfSymbolInfo->marketing_agent = $result['eft_info']['fund_info']['Marketing Agent'] ?? null;
            $etfSymbolInfo->gold_custodian = $result['eft_info']['fund_info']['Gold Custodian'] ?? null;
            $etfSymbolInfo->sponsor = $result['eft_info']['fund_info']['Sponsor'] ?? null;
            $etfSymbolInfo->exchange = $result['eft_info']['listing_info']['Exchange'] ?? null;
            $etfSymbolInfo->listing_date = $result['eft_info']['listing_info']['Listing Date'] ?? null;
            $etfSymbolInfo->trading_currency = $result['eft_info']['listing_info']['Trading Currency'] ?? null;
            $etfSymbolInfo->CUSIP = $result['eft_info']['listing_info']['CUSIP'] ?? null;
            $etfSymbolInfo->ISIN = $result['eft_info']['listing_info']['ISIN'] ?? null;

            $etfSymbolInfo->save();
        }
        


    }

}
