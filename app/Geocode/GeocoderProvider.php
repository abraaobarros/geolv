<?php

namespace GeoLV\Geocode;

use Geocoder\Model\AddressCollection;
use Geocoder\Provider\ArcGISOnline\ArcGISOnline;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\ProviderAggregator;
use Geocoder\Query\GeocodeQuery;
use GeoLV\Address;
use Http\Adapter\Guzzle6\Client;
use Illuminate\Support\Collection;
use TomLingham\Searchy\SearchDrivers\FuzzySearchDriver;

class GeocoderProvider
{
    protected $aggregator;
    protected $limit;
    protected $results;
    protected $adapter;
    protected $searchDriver;
    protected $relevanceFieldName = 'relevance';
    protected $searchColumns = [
        'street_name::street_number::sub_locality::locality::country_name',
        'street_name::street_number::sub_locality::locality',
        'street_name::street_number::sub_locality',
        'street_name::street_number',
        'street_name',
        'locality',
        'sub_locality',
        'postal_code',
        'country_name',
        'country_code',
    ];

    public function __construct()
    {
        $this->results = new AddressCollection();
        $this->aggregator = new ProviderAggregator();
        $this->adapter = Client::createWithConfig(['verify' => false]);
        $this->searchDriver = new FuzzySearchDriver('addresses', $this->searchColumns, $this->relevanceFieldName);

        $this->registerProviders([
            new SearchResults($this->searchDriver,
                new GroupResults([
                    Nominatim::withOpenStreetMapServer($this->adapter),
                    new GoogleMaps($this->adapter, 'pt-BR', env('GOOGLE_MAPS_API_KEY')),
                    new ArcGISOnline($this->adapter)
                ])
            )
        ]);
    }

    public function get() : Collection
    {
        return Address::hydrate($this->results->all());
    }

    public function geocodeQuery(GeocodeQuery $query) : self
    {
        $this->results = $this->aggregator->geocodeQuery($query);
        return $this;
    }

    public function geocode(string $value) : self
    {
        $this->results = $this->aggregator->geocode($value);
        return $this;
    }

    protected function registerProviders(array $providers = []) : self
    {
        $this->aggregator->registerProviders($providers);

        return $this;
    }

}