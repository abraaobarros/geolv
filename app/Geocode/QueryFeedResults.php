<?php

namespace GeoLV\Geocode;

use GeoLV\Geocode\Dictionary;
use Geocoder\Collection;
use Geocoder\Exception\UnsupportedOperation;
use Geocoder\Model\AddressCollection;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

class QueryFeedResults implements Provider
{
    private $provider;
    private $dictionary;

    /**
     * QueryFeedResults constructor.
     * @param Provider $provider
     */
    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
        $this->dictionary = new Dictionary();
    }

    public function geocodeQuery(GeocodeQuery $query): Collection
    {
        $results = collect();
        $matches = $this->dictionary->getMatchingQueries($query->getText());

        foreach ($matches as $q) {
            $providerResults = $this->provider->geocodeQuery(GeocodeQuery::create($q));
            $results = $results->merge($providerResults->all());
        }

        return new AddressCollection($results->sortByDesc('relevance')->values()->toArray());
    }

    public function reverseQuery(ReverseQuery $query): Collection
    {
        throw new UnsupportedOperation();
    }

    public function getName(): string
    {
        return 'feed';
    }

}