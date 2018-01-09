<?php

namespace GeoLV\Http\Controllers;

use GeoLV\Address;
use GeoLV\Geocode\Dictionary;
use GeoLV\Http\Requests\GeocodingRequest;
use GeoLV\Search;
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
        $results = $this->geocoder->geocode($match)->get();
        $search = Search::findFromText($match);

        return view('geocode', compact('results', 'search'))->with($request->all());
    }

    public function map(Request $request)
    {
        $search = Search::findOrFail($request->get('search_id'));
        $selected = Address::findOrFail($request->get('selected_id'));
        $results = $this->geocoder->geocode($search->text)->get();

        return view('map', compact('results', 'selected'));
    }

    public function preload()
    {
        return view('preload');
    }


}
