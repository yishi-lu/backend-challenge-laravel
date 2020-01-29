<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 

use App\Contracts\Business\EtfsService;


/**
 * EtfsController
 *
 * Created by Yishi Lu.
 * User: Yishi Lu
 * Date: 2020/01/27
 */
class EtfsController extends Controller
{
    protected $service;
    public $successStatus = 200;

    /**
     * EtfsController constructor, get EtfsService object by DI
     * @param $service
     */
    public function __construct(EtfsService $service)
    {
        $this->service = $service;
    }

    /**
     * call EtfsService to fetch all ETF symbols
     *
     * @param null
     * @return json response
     */
    public function fetchAllEtfs(){

        $result = $this->service->getAllEtfs();

        return response()->json(['success'=>$result], $this->successStatus); 
    }

    /**
     * call EtfsService to fetch detail information of a ETF symbol by ticker
     *
     * @param ticker, request
     * @return json response
     */
    public function getEtfByTicker($ticker, Request $request){

        $result = $this->service->getEtfByTicker($ticker);

        if($result) return response()->json(['success'=>$result], $this->successStatus); 
        else return response()->json(['error'=>'invalid ticker'], 401); 

    }
}
