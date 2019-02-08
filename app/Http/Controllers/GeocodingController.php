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

    /** @var array */
    private $defaultProviders;

    /**
     * GeocodingController constructor.
     */
    public function __construct()
    {
        $this->geocoder = app('geocoder');
        $this->defaultProviders = ['google_maps', 'here_geocoder'];
    }

    public function index()
    {
        $results = collect();
        $match = null;
        $localities = Locality::get(['name', 'state']);
        $providers = ['google_maps', 'here_geocoder', 'bing_maps', 'arcgis_online'];
        $selectedProviders = session('geocode.default_providers', $this->defaultProviders);

        return view('geocode', compact('results', 'match', 'localities', 'providers', 'selectedProviders'));
    }

    public function geocode(GeocodingRequest $request)
    {
        $text = Dictionary::address($request->get('text'));
        $locality = trim($request->get('locality'));
        $postalCode = $request->get('postal_code');
        $localities = Locality::get(['name', 'state']);
        $providers = ['google_maps', 'here_geocoder', 'bing_maps', 'arcgis_online'];
        $selectedProviders = $request->get('providers', $this->defaultProviders);
        session(['geocode.default_providers' => $selectedProviders]);

        $this->geocoder->setProviders($selectedProviders);
        $results = $this->geocoder->geocode($text, $locality, $postalCode);
        $outside = !empty($locality) ? $results->outsideLocality() : new AddressCollection();
        $clustersCount = $results->getClustersCount();
        $providersCount = $results->inMainCluster()->getProvidersCount();
        $results = !empty($locality) ? $results->insideLocality() : $results;
        $dispersion = $results->inMainCluster()->calculateDispersion();
        $precision = $results->inMainCluster()->calculatePrecision();
        $confidence = $results->calculateConfidence();

        return view('geocode', compact('results', 'text', 'locality', 'postalCode', 'localities',
            'outside', 'dispersion', 'clustersCount', 'providersCount', 'providers', 'selectedProviders', 'precision',
            'confidence'));
    }

    public function map(Request $request)
    {
        $search = Search::findOrFail($request->get('search_id'));
        $selected = Address::findOrFail($request->get('selected_id'));
        $providers = $request->get('providers', $this->defaultProviders);
        $search->max_d = $request->get('max_d', Search::DEFAULT_MAX_D);

        $this->geocoder->setProviders($providers);
        $results = $this->geocoder->get($search)->insideLocality();
        $dispersion = $results->inMainCluster()->calculateDispersion();
        $precision = $results->inMainCluster()->calculatePrecision();
        $clustersCount = $results->getClustersCount();
        $providersCount = $results->inMainCluster()->getProvidersCount();
        $confidence = $results->calculateConfidence();

        return view('map', compact('results', 'selected', 'search', 'providers', 'dispersion',
            'precision', 'clustersCount', 'providersCount', 'confidence'));
    }

}
