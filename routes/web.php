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

use Illuminate\Http\Request;

Route::get('/', function (Request $request) {
    if ($request->has('address')) {
        $results = app('geocoder')
            ->geocode($request->get('address'))
            ->get();
    } else {
        $results = collect();
    }

    return view('geocode', compact('results'));
});

