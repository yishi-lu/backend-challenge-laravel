<?php

namespace App\Contracts\Business;

/**
 * Etfs Service interface
 *
 * Created by Yishi Lu.
 * User: Yishi Lu
 * Date: 2020/01/27
 */

interface EtfsService
{
    //fetch all etf symbols 
    public function getAllEtfs();

    //fetch etf symbol by given ticker
    public function getEtfByTicker($ticker);

    //update etf symbols from given url
    function updateEtfs($url);

    //update detail information of etfs by given etf symbol and url
    function updateEtfsAttributes($etfSymbol, $fundUri);

}