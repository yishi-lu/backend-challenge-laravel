<?php

namespace App\Services\Business;

use App\Contracts\Business\ParserService;
use KubAT\PhpSimple\HtmlDomParser;

/**
 * ParserServiceImp
 *
 * Created by Yishi Lu.
 * User: Yishi Lu
 * Date: 2020/01/27
 */
class ParserServiceImp implements ParserService{

    private $ch;

    public function __construct(){
        $this->ch = curl_init();
    }

    /**
     * parse etfs information from url
     *
     * @param url
     * @return array
     */
    public function parseEtfs($url){
        try {
        
            //check if initialization had gone wrong
            if ($this->ch === false) {
                throw new Exception('failed to initialize');
            }
        
            curl_setopt($this->ch, CURLOPT_URL, $url);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        
            $content = curl_exec($this->ch);
        
            //check the return value of curl_exec()
            if ($content === false) {
                throw new Exception(curl_error($this->ch), curl_errno($this->ch));
            }
        
            //close curl handle
            curl_close($this->ch);
            
            $result = json_decode($content);

            return $result;

        } catch(Exception $e) {
            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);
        }
    }

    /**
     * parse etfs symbol detail information from url
     *
     * @param url
     * @return array
     */
    public function parseEtfDetail($url){
        $html = HtmlDomParser::file_get_html($url);

        $fundTopGoldingsArray = $this->findTopHolding($html, "fund-top-holdings");
        $indexTopHoldingsArray = $this->findTopHolding($html, "index-top-holdings");

        $fundSectorWeightArray = $this->findSectorWeight($html, "fund-sector-breakdown");
        $indexSectorWeightArray = $this->findSectorWeight($html, "index-sector-breakdown");

        $countryWeightArray = $this->findCountryWeight($html);

        $eftInfoArray = $this->findInfo($html);

        
        $result = array();
        $result['fund_top_holdings'] = $fundTopGoldingsArray;
        $result['index_top_holdings'] = $indexTopHoldingsArray;
        $result['fund_sector_weight'] = $fundSectorWeightArray;
        $result['index_sector_weight'] = $indexSectorWeightArray;
        $result['country_weight'] = $countryWeightArray;
        $result['eft_info'] = $eftInfoArray;

        return $result;
    }

    /**
     * parse top holding information by given dom object and class name
     *
     * @param html, name
     * @return array
     */
    public function findTopHolding($html, $name){
        $topHoldingArray = array();
        
        $topHolding = $html->find('div.'.$name, 0);

        if($topHolding == null) return null;

        $topHoldingItems = $topHolding->find('tr');

        $head = $topHolding->find('th');

        foreach($topHoldingItems as $item){

            $attributes = $item->find('td');

            if($attributes != null){
                $entry = array();

                for($i=0; $i<sizeof($head); $i++){

                    switch($head[$i]->innertext){

                        case 'Name':
                            $entry['name'] = $attributes[$i]->innertext;
                            break;
                        case 'Ticker':
                            $entry['ticker'] = $attributes[$i]->innertext;
                            break;
                        case 'Shares Held':
                            $entry['shares'] = $attributes[$i]->innertext;
                            break;
                        case 'Market Value':
                            $entry['market_value'] = $attributes[$i]->innertext;
                            break;
                        case 'Par Value':
                            $entry['par_value'] = $attributes[$i]->innertext;
                            break;
                        case 'Total Mkt Cap M':
                            $entry['total_mkt_cap_m'] = $attributes[$i]->innertext;
                            break;
                        case 'Weight':
                            $entry['weight'] = $attributes[$i]->innertext;
                            break;
                        case 'ISIN':
                            $entry['ISIN'] = $attributes[$i]->innertext;
                            break;
                        default:
                            break;  
                    }
                }

                array_push($topHoldingArray, $entry);
            }

        }
        return $topHoldingArray;
    }

    /**
     * parse sector weight information by given dom object and class name
     *
     * @param html, name
     * @return array
     */
    public function findSectorWeight($html, $name){

        $sectorWeightArray = array();

        $sectorWeight = $html->find('div.'.$name, 0);

        if($sectorWeight == null) return null;

        $sectorWeightItems = $sectorWeight->find('td');
        
        for($i=0; $i<sizeof($sectorWeightItems); $i=$i+2){

            $entry = array();
            $entry['label'] = $sectorWeightItems[$i]->innertext;
            $entry['data'] = $sectorWeightItems[$i+1]->innertext;

            array_push($sectorWeightArray, $entry);
        }

        return $sectorWeightArray;
    }

    /**
     * parse country weight information by given dom object
     *
     * @param html
     * @return array
     */
    public function findCountryWeight($html){

        $countryWeight = $html->find("input[id=fund-geographical-breakdown]", 0);

        if($countryWeight == null) return null;

        $values = $countryWeight->value;

        $values = json_decode(str_replace("&#34;", '"', $values));

        return $values->attrArray;
    }

    /**
     * parse description and information of a etf symbol by given dom object
     *
     * @param html
     * @return array
     */
    public function findInfo($html){

        $eftInfoArray = array();

        $keyFeature = $html->find('div.fundcontent', 2);
        $eftInfoArray['key_feature'] = $keyFeature->find('div.content', 0)->innertext;

        $about = $html->find('div.fundcontent', 4);
        $eftInfoArray['about'] = $about->find('div.content', 0) ? $about->find('div.content', 0)->innertext : null;

        //find Fund Information
        $info = $html->find('div.keyvalue ', 0);

        $infoDetial1 = $info->find('tbody', 1)->find('td');
        $infoDetial2 = $info->find('tbody', 3)->find('td');
        $infoArray = array();
        for($i=0; $i<sizeof($infoDetial1); $i=$i+2){

            if(strpos($infoDetial1[$i]->innertext, 'Gross Expense Ratio') !== false) {
                $infoDetial1[$i]->innertext = 'Gross Expense Ratio';
                $infoArray['Gross Expense Ratio'] = $infoDetial1[$i+1]->innertext;
            }
            else $infoArray[$infoDetial1[$i]->innertext] = $infoDetial1[$i+1]->innertext;
        }
        for($i=0; $i<sizeof($infoDetial2); $i=$i+2){

            $infoArray[$infoDetial2[$i]->innertext] = $infoDetial2[$i+1]->innertext;
        }
        $eftInfoArray['fund_info'] = $infoArray;

        //find Listing Information
        $info = $html->find('div.keyvalue ', 1);
        $infoDetial = $info->find('tbody', 1)->find('td');
        $infoArray = array();
        for($i=0; $i<sizeof($infoDetial); $i=$i+2){
            $infoArray[$infoDetial[$i]->innertext] = $infoDetial[$i+1]->innertext;
        }

        $eftInfoArray['listing_info'] = $infoArray;

        return $eftInfoArray;
    }
}
