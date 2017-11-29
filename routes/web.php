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

Route::get('/', function (Request $request) {
    if ($request->has('address')) {
        $address = $request->get('address');
        $results = app('geocoder')
            ->geocode($request->get('address'))
            ->get();
        $matches = (new Dictionary())->getMatchingQueries($address);
    } else {
        $results = collect();
        $matches = collect();
    }

    return view('geocode', compact('results', 'matches'))->with($request->all());
});

