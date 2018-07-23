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
Route::get('/createbot', 'ApiController@createBot');

Route::get('/createlobby', 'ApiController@createLobby');
Route::post('/getRequestCreateLobby', 'ApiController@getRequestCreateLobby');
Route::get('/botsdestroy', 'ApiController@botsdestroy');

Route::post('/botresponse', 'ApiController@botResponse');
Route::get('/testbotresponse', 'ApiController@testBotResponse');
Route::get('/processtournament', 'ApiController@processTournament');


Route::post('/qiwi', 'QiwiController@result');

//CRON funciton для создания сетки турнира и движения по этапам
Route::post('/stage/{pass}', 'AdminController@nextStageCron')->name('nextStageCron');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
