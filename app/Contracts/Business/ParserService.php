<?php

namespace App\Contracts\Business;

/**
 * Parser Service interface
 *
 * Created by Yishi Lu.
 * User: Yishi Lu
 * Date: 2020/01/27
 */

interface ParserService
{
    //parse etfs information from url
    public function parseEtfs($url);

    //parse etfs symbol detail information from url
    public function parseEtfDetail($url);

    //parse top holding information by given dom object and class name
    public function findTopHolding($html, $name);

    //parse sector weight information by given dom object and class name
    public function findSectorWeight($html, $name);

    //parse country weight information by given dom object
    public function findCountryWeight($html);

    //parse description and information of a etf symbol by given dom object
    public function findInfo($html);

}