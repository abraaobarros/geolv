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

/** @var \Illuminate\Routing\Router $router */

$router->get('/', 'GeocodingController@index')->name('index');
$router->get('/geocode', 'GeocodingController@geocode')->name('geocode');
$router->get('/geocode/map', 'GeocodingController@map')->name('map');
$router->resource('files', 'GeocodingFileController')->only(['create', 'store', 'show']);
$router->get('files/{files}/email', 'GeocodingFileController@email');

