<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Auth router
Route::name('api.auth')->namespace('Api')->prefix('auth')->group(function(){
    Route::post("/registration", "AuthController@registration")->name('registration');
    Route::post("/login", "AuthController@login")->name('login');
});

Route::name('api.auth')->namespace('Api')->prefix('auth')->middleware('auth:api')->group(function(){
    Route::get("/logout", "AuthController@logout")->name('logout');
});

//Etfs router
Route::name('api.etfs')->namespace('Api')->prefix('etfs')->middleware('auth:api')->group(function(){
    Route::get("/all", "EtfsController@fetchAllEtfs")->name('all');
    Route::get("/etfDetail/{ticker}", "EtfsController@getEtfByTicker")->name('etfDetail');
});