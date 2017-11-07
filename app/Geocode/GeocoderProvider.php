<?php

namespace App\Geocode;

use Geocoder\Provider\Cache\ProviderCache;
use Geocoder\Provider\Chain\Chain;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\ProviderAggregator;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use Http\Adapter\Guzzle6\Client;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Support\Collection;

class GeocoderProvider
{
    protected $aggregator;
    protected $limit;
    protected $results;
    protected $adapter;
    private $cacheDuration;

    public function __construct()
    {
        $this->aggregator = new ProviderAggregator();
        $this->results = collect();
        $this->adapter = Client::createWithConfig(['verify' => false]);
        $this->cacheDuration = 9999999;

        $this->registerProviders([
            Nominatim::withOpenStreetMapServer($this->adapter),
            new GoogleMaps($this->adapter, 'pt-BR', env('GOOGLE_MAPS_API_KEY')),
        ]);
    }

    public function get() : Collection
    {
        return $this->results;
    }

    public function geocodeQuery(GeocodeQuery $query) : self
    {
        $cacheKey = serialize($query);
        $this->results = app('cache')->remember(
            "geocoder-{$cacheKey}",
            $this->cacheDuration,
            function () use ($query) {
                return collect($this->aggregator->geocodeQuery($query));
            }
        );

        $this->removeEmptyCacheEntry("geocoder-{$cacheKey}");

        return $this;
    }

    public function reverseQuery(ReverseQuery $query) : self
    {
        $cacheKey = serialize($query);
        $this->results = app('cache')->remember(
            "geocoder-{$cacheKey}",
            $this->cacheDuration,
            function () use ($query) {
                return collect($this->aggregator->reverseQuery($query));
            }
        );

        $this->removeEmptyCacheEntry("geocoder-{$cacheKey}");

        return $this;
    }

    public function geocode(string $value) : self
    {
        $cacheKey = str_slug(strtolower(urlencode($value)));
        $this->results = app('cache')->remember(
            "geocoder-{$cacheKey}",
            $this->cacheDuration,
            function () use ($value) {
                return collect($this->aggregator->geocode($value));
            }
        );

        $this->removeEmptyCacheEntry("geocoder-{$cacheKey}");

        return $this;
    }

    public function reverse(float $latitude, float $longitude) : self
    {
        $cacheKey = str_slug(strtolower(urlencode("{$latitude}-{$longitude}")));
        $this->results = app('cache')->remember(
            "geocoder-{$cacheKey}",
            $this->cacheDuration,
            function () use ($latitude, $longitude) {
                return collect($this->aggregator->reverse($latitude, $longitude));
            }
        );

        $this->removeEmptyCacheEntry("geocoder-{$cacheKey}");

        return $this;
    }


    public function registerProvider($provider) : self
    {
        $this->aggregator->registerProvider($provider);

        return $this;
    }

    public function registerProviders(array $providers = []) : self
    {
        $this->aggregator->registerProviders($providers);

        return $this;
    }

    public function using(string $name) : self
    {
        $this->aggregator = $this->aggregator->using($name);

        return $this;
    }

    protected function removeEmptyCacheEntry(string $cacheKey)
    {
        try {
            $result = app('cache')->get($cacheKey);
        } catch (EntryNotFoundException $e) {
            $result = null;
        }

        if ($result && $result->isEmpty()) {
            app('cache')->forget($cacheKey);
        }
    }
}