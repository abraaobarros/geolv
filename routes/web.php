<?php

use Illuminate\Routing\Router;

/** @var Router $router */

Auth::routes(['verify' => true]);

$router->group(['middleware' => 'guest'], function (Router $router) {
    $router->get('/', 'Auth\LoginController@showLoginForm');
});

$router->group(['middleware' => ['auth', 'verified']], function (Router $router) {

    $router
        ->get('/home', 'GeocodingController@index')
        ->name('home');

    $router
        ->get('/geocode', 'GeocodingController@geocode')
        ->name('geocode');

    $router
        ->get('/geocode/map', 'GeocodingController@map')
        ->name('map');

    $router
        ->resource('files', 'GeocodingFileController')
        ->except(['show', 'edit', 'update']);

    $router
        ->get('files/{files}/download', 'GeocodingFileController@download')
        ->name('files.download');

    $router
        ->get('files/{files}/download-errors', 'GeocodingFileController@downloadErrors')
        ->name('files.download-errors');

    $router
        ->post('files/{files}/prioritize', 'GeocodingFileController@prioritize')
        ->name('files.prioritize');

    $router
        ->post('files/{files}/cancel', 'GeocodingFileController@cancel')
        ->name('files.cancel');

    $router
        ->resource('users', 'UsersController');

});
