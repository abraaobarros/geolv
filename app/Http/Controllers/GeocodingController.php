<?php

namespace GeoLV\Http\Controllers;

use GeoLV\Geocode\Dictionary;
use GeoLV\Http\Requests\GeocodingRequest;
use Illuminate\Http\Request;

class GeocodingController extends Controller
{
    private $geocoder;

    /**
     * GeocodingController constructor.
     */
    public function __construct()
    {
        $this->geocoder = app('geocoder');
    }

    public function index()
    {
        $results = collect();
        $match = null;

        return view('geocode', compact('results', 'match'));
    }

    public function geocode(GeocodingRequest $request)
    {
        $dictionary = new Dictionary();
        $address = join(" ", $request->only(['street_name', 'locality', 'cep']));
        $match = $dictionary->getMatchingQuery($address);
        $results = $this->geocoder
            ->geocode($address)
            ->get();

        return view('geocode', compact('results', 'match'))->with($request->all());
    }
}
