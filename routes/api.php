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

Route::group(['middleware' => 'web'], function () {

    Route::get('qq', 'IndexController@qq');
    Route::get('callback', 'IndexController@callback');

});


Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {

    Route::post('login', 'IndexController@login')->name('login');
    Route::post('logout', 'IndexController@logout');
    Route::post('refresh', 'IndexController@refresh');
    Route::post('me', 'IndexController@me');

});

