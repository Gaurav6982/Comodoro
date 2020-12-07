<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('register', 'JWTAuthController@register');
    Route::post('login', 'JWTAuthController@sendOtp');
    Route::post('logout', 'JWTAuthController@logout');
    Route::post('refresh', 'JWTAuthController@refresh');
    Route::get('profile', 'JWTAuthController@profile');
    // Route::put('update', 'JWTAuthController@update');
});

Route::group([
    'middleware' => 'api',
    
], function ($router) {
    Route::get('get-events','EventController@all');
    Route::resource('event','EventController');
    Route::resource('event/reminder','ReminderController');
    Route::get('reg','MainController@index');
    Route::put('update/{email}','JWTAuthController@update');
    Route::delete('delete','JWTAuthController@del');
    Route::post('verifyOtp', 'JWTAuthController@verifyOtp');
    Route::get('checkVerify', 'JWTAuthController@checkVerify');
});