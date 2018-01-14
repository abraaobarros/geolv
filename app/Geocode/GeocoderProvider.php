<?php

namespace GeoLV\Geocode;

use Geocoder\Location;
use Geocoder\Provider\ArcGISOnline\ArcGISOnline;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\ProviderAggregator;
use Geocoder\Query\GeocodeQuery;
use GeoLV\Address;
use GeoLV\Search;
use Http\Adapter\Guzzle6\Client;

class GeocoderProvider
{
    private $provider;
    private $adapter;
    private $searchDriver;

    public function __construct()
    {
        $this->provider = new ProviderAggregator();
        $this->adapter = Client::createWithConfig(['verify' => false]);
        $this->searchDriver = new GeoLVSearch();

        $this->provider->registerProviders([
            new GroupResults([
                new GoogleMaps($this->adapter, 'pt-BR', env('GOOGLE_MAPS_API_KEY')),
                new ArcGISOnline($this->adapter, 'BRA'),
                new HereGeocoder($this->adapter, env('HERE_GEOCODER_ID'), env('HERE_GEOCODER_CODE'))
            ])
        ]);
    }

    public function geocode($text, $locality, $postalCode)
    {
        $search = $this->getSearch($text, $locality, $postalCode);
        return $this->get($search);
    }

    public function get(Search $search)
    {
        return $this->searchDriver->search($search);
    }

    private function getSearch($text, $locality, $postalCode): Search
    {
        $query = GeocodeQuery::create(trim(join(" ", [$text, $locality, $postalCode])));
        $results = $this->provider->geocodeQuery($query);
        $search = Search::firstOrCreate([
            'text' => filled($text)? $text : null,
            'locality' => filled($locality)? $locality : null,
            'postal_code' => filled($postalCode)? $postalCode : null
        ]);

        /** @var Location $result */
        foreach ($results as $result) {
            if (empty($result->getStreetName()))
                continue;

            $address = Address::firstOrCreate([
                'street_name' => $result->getStreetName(),
                'street_number' => $result->getStreetNumber(),
                'locality' => $result->getLocality(),
                'postal_code' => $result->getPostalCode(),
                'sub_locality' => $result->getSubLocality(),
                'country_code' => $result->getCountry()->getCode(),
                'country_name' => $result->getCountry()->getName(),
                'latitude' => $result->getCoordinates()->getLatitude(),
                'longitude' => $result->getCoordinates()->getLongitude(),
                'provider' => $result->getProvidedBy(),
            ]);

            $search->addresses()->attach($address->id);
        }

        return $search;
    }


}