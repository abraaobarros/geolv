<?php

namespace GeoLV\Http\Controllers;

use GeoLV\Address;
use GeoLV\Geocode\Dictionary;
use GeoLV\Geocode\GeocoderProvider;
use GeoLV\GeocodingFile;
use GeoLV\Http\Requests\GeocodingRequest;
use GeoLV\Http\Requests\UploadRequest;
use GeoLV\Jobs\ProcessGeocodingFile;
use GeoLV\Locality;
use GeoLV\Search;
use Illuminate\Http\Request;

class GeocodingController extends Controller
{
    /** @var GeocoderProvider  */
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
        $localities = Locality::all();

        return view('geocode', compact('results', 'match', 'localities'));
    }

    public function geocode(GeocodingRequest $request)
    {
        $text = Dictionary::address($request->get('text'));
        $locality = $request->get('locality');
        $postalCode = $request->get('postal_code');
        $localities = Locality::all();

        $results = $this->geocoder->geocode($text, $locality, $postalCode);
        $outside = $results->outsideLocality();
        $results = $results->insideLocality();
        $dispersion = $results->inMainCluster()->calculateDispersion();

        return view('geocode', compact('results', 'text', 'locality', 'postalCode', 'localities', 'outside', 'dispersion'));
    }

    public function map(Request $request)
    {
        $search = Search::findOrFail($request->get('search_id'));
        $selected = Address::findOrFail($request->get('selected_id'));
        $results = $this->geocoder->get($search)->insideLocality();

        return view('map', compact('results', 'outside', 'selected', 'search'));
    }

}
