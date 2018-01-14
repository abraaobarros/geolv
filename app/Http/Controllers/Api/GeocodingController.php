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
        $text = ucwords((new Dictionary())->getMatchingQuery($request->get('text')));
        $locality = $request->get('locality');
        $postalCode = $request->get('postal_code');

        $results = $this->geocoder
            ->geocode($text, $locality, $postalCode)
            ->get()
            ->map(function (Address $address) {
                return array_except($address->toArray(), ['id', 'created_at', 'updated_at', 'text']);
            });

        return $this->api($results);
    }
}
