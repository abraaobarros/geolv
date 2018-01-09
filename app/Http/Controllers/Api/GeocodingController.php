<?php

namespace GeoLV\Http\Controllers\Api;

use GeoLV\Address;
use GeoLV\Geocode\Dictionary;
use GeoLV\Http\Requests\GeocodingRequest;
use Illuminate\Http\Request;
use GeoLV\Http\Controllers\Controller;

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

    public function geocode(GeocodingRequest $request)
    {
        $address = join(" ", $request->only(['street_name', 'locality', 'cep']));
        $match = (new Dictionary())->getMatchingQuery($address);
        $results = $this->geocoder->geocode($match)->get()->map(function (Address $address) {
            return array_except($address->toArray(), ['id', 'created_at', 'updated_at', 'text']);
        });

        return $this->api($results);
    }
}
