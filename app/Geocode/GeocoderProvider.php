<?php

namespace GeoLV\Geocode;

use Geocoder\Exception\UnsupportedOperation;
use Geocoder\Provider\BingMaps\BingMaps;
use Geocoder\Provider\Provider;
use GeoLV\AddressCollection;
use Geocoder\Location;
use Geocoder\Provider\ArcGISOnline\ArcGISOnline;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\ProviderAggregator;
use Geocoder\Query\GeocodeQuery;
use GeoLV\Address;
use GeoLV\Geocode\Clusters\ClusterWithScipy;
use GeoLV\Search;
use Http\Adapter\Guzzle6\Client;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;

class GeocoderProvider
{
    /** @var ProviderAggregator */
    private $provider;
    private $adapter;
    private $searchDriver;
    private $user;
    private $defaultProviders;
    private $providers;

    const LOW_COST_STRATEGY = GroupResults::LOW_COST_STRATEGY;
    const HIGH_COST_STRATEGY = GroupResults::HIGH_COST_STRATEGY;

    public function __construct()
    {
        $this->defaultProviders = ['google_maps', 'here_geocoder'];
        $this->adapter = Client::createWithConfig(['verify' => false]);
        $this->searchDriver = new GeoLVSearch();
        $this->user = auth()->user();

        $this->setProviders(static::HIGH_COST_STRATEGY);
    }

    /**
     * @param int $strategy LOW_COST_STRATEGY|HIGH_COST_STRATEGY
     * @param array|null $providers
     */
    public function setProviders(int $strategy, array $providers = null): void
    {
        $this->provider = new ProviderAggregator();
        $this->providers = empty($providers)? $this->defaultProviders : $providers;
        $config = [];

        foreach ($this->providers as $provider) {
            $config[] = $this->resolveProvider($provider);
        }

        $this->provider->registerProvider(new GroupResults($strategy, $config));
    }

    /**
     * @param $text
     * @param $locality
     * @param $postalCode
     * @return AddressCollection
     */
    public function geocode($text, $locality, $postalCode): AddressCollection
    {
        $search = $this->getSearch($text, $locality, $postalCode);

        return $this->get($search);
    }

    public function get(Search $search): AddressCollection
    {
        $results = $this->geocodeResults($search);

        $sorter = new SortByRelevance($search);
        $results = $sorter->apply($results);

        $groupper = new ClusterWithScipy();
        //$groupper = new ClusterByAverage();
        //$groupper = new ClusterWithKMeans();
        $groupper->apply($results, $search->max_d);

        return $results->values();
    }

    /**
     * @param $text
     * @param $locality
     * @param $postalCode
     * @return Search
     */
    private function getSearch($text, $locality, $postalCode): Search
    {
        $search = Search::firstOrCreate([
            'text' => filled($text)? $text : null,
            'locality' => filled($locality)? $locality : null,
            'postal_code' => filled($postalCode)? $postalCode : null,
        ]);

        return $search;
    }

    /**
     * @return GoogleMaps
     */
    private function getGoogleProvider(): GoogleMaps
    {
        return new GoogleMaps($this->adapter, 'pt-BR', env('GOOGLE_MAPS_API_KEY'));
    }

    /**
     * @return ArcGISOnline
     */
    private function getArcGISOnlineProvider(): ArcGISOnline
    {
        return new ArcGISOnline($this->adapter, 'BRA');
    }

    /**
     * @return HereGeocoder
     */
    private function getHereGeocoderProvider(): HereGeocoder
    {
        return new HereGeocoder($this->adapter, env('HERE_GEOCODER_ID'), env('HERE_GEOCODER_CODE'));
    }

    /**
     * @return BingMaps
     */
    private function getBingMapsProvider(): BingMaps
    {
        return new BingMaps($this->adapter, env('BING_MAPS_API_KEY'));
    }

    /**
     * @param $provider
     * @return Provider
     * @throws UnsupportedOperation
     */
    private function resolveProvider($provider): Provider
    {
        switch ($provider) {
            case 'google_maps':
                return $this->getGoogleProvider();
            case 'arcgis_online':
                return $this->getArcGISOnlineProvider();
            case 'here_geocoder':
                return $this->getHereGeocoderProvider();
            case 'bing_maps':
                return $this->getBingMapsProvider();
            default:
                throw new UnsupportedOperation("Unsupported provider $provider.");
        }
    }

    /**
     * @param Search $search
     * @return Collection|AddressCollection
     */
    private function geocodeResults(Search $search)
    {
        try {
            $results = $this->provider->geocodeQuery(GeocodeQuery::create($search->address));
        } catch (\Geocoder\Exception\Exception $e) {
            $results = [];
        }

        $collection = new AddressCollection();

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

            $address->search_id = $search->id;

            try {
                $search->addresses()->attach($address->id);
            } catch (QueryException $exception) {}

            $collection->add($address);
        }

        return $collection;
    }

}