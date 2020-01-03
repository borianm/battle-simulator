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

Route::post('/games', 'API\GameController@create')->name('games_post');
Route::get('/games', 'API\GameController@index')->name('games_get');
Route::get('/games/{id}', 'API\GameController@show')->name('games_show');
Route::get('/games/{id}/reset', 'API\GameController@reset')->name('games_reset');
Route::post('/games/{id}/run', 'API\GameController@run')->name('games_run');
Route::get('/games/{id}/log', 'API\BattleLogController@index')->name('games_log');
Route::post('/games/{id}/army', 'API\ArmyController@create')->name('games_army');