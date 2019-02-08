<?php

namespace GeoLV\Http\Controllers\Api;

use GeoLV\Geocode\Dictionary;
use GeoLV\Geocode\GeocoderProvider;
use GeoLV\Http\Controllers\Controller;
use GeoLV\Http\Requests\GeocodingRequest;
use GeoLV\Http\Resources\AddressCollection as AddressCollectionResource;

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

    public function geocode(GeocodingRequest $request)
    {
        $text = Dictionary::address($request->get('text'));
        $locality = $request->get('locality');
        $postalCode = $request->get('postal_code');
        $providers = $request->get('providers');

        $this->geocoder->setProviders($providers);
        $results = $this->geocoder->geocode($text, $locality, $postalCode);

        return new AddressCollectionResource($results);
    }

}
