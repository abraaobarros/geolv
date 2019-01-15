<?php

use Illuminate\Routing\Router;

/** @var Router $router */

Auth::routes(['verify' => true]);

$router->group(['middleware' => 'guest'], function (Router $router) {
    $router->get('/', 'Auth\LoginController@showLoginForm');
});

$router->group(['middleware' => 'auth'], function (Router $router) {

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
        ->except(['edit', 'update']);

    $router
        ->get('files/{files}/email', 'GeocodingFileController@email');

});
