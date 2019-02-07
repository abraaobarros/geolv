<?php

namespace GeoLV\Http\Controllers;

use GeoLV\Address;
use GeoLV\AddressCollection;
use GeoLV\Geocode\Dictionary;
use GeoLV\Geocode\GeocoderProvider;
use GeoLV\Http\Requests\GeocodingRequest;
use GeoLV\Locality;
use GeoLV\Search;
use Illuminate\Http\Request;

class GeocodingController extends Controller
{
    /** @var GeocoderProvider */
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
        $localities = Locality::get(['name', 'state']);

        return view('geocode', compact('results', 'match', 'localities'));
    }

    public function geocode(GeocodingRequest $request)
    {
        $text = Dictionary::address($request->get('text'));
        $locality = trim($request->get('locality'));
        $postalCode = $request->get('postal_code');
        $localities = Locality::get(['name', 'state']);

        $results = $this->geocoder->geocode($text, $locality, $postalCode);
        $outside = !empty($locality)? $results->outsideLocality() : new AddressCollection();
        $clustersCount = $results->getClustersCount();
        $providersCount = $results->inMainCluster()->getProvidersCount();

        $results = !empty($locality)? $results->insideLocality() : $results;
        $dispersion = $results->inMainCluster()->calculateDispersion();

        return view('geocode', compact('results', 'text', 'locality', 'postalCode', 'localities', 'outside', 'dispersion', 'clustersCount', 'providersCount'));
    }

    public function map(Request $request)
    {
        $search = Search::findOrFail($request->get('search_id'));
        $selected = Address::findOrFail($request->get('selected_id'));
        $search->max_d = $request->get('max_d', Search::DEFAULT_MAX_D);

        $results = $this->geocoder->get($search)->insideLocality();

        return view('map', compact('results', 'selected', 'search'));
    }

}
