<?php

/** @var \Illuminate\Routing\Router|\Illuminate\Routing\Route $router */

$router
    ->get('geocode', 'Api\GeocodingController@geocode')
    ->middleware('auth.basic');



