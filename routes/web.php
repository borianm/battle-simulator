<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Models\AttackStrategy;
use App\Models\Game;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('index');
});
Route::get('/games/{id}', function (Request $request, $id) {
    if (Game::find($id) === null) abort(404);
    $strategies = AttackStrategy::all();
    return view('game', compact('id', 'strategies'));
});
