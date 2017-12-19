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

use GeoLv\Geocode\Dictionary;
use Illuminate\Http\Request;

Route::get('/', 'GeocodingController@index');
Route::get('/geocode', 'GeocodingController@geocode');
Route::get('/geocode/map', 'GeocodingController@map')->name('map');

