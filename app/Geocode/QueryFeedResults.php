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
        $queryText = $query->getText();
        $match = $this->dictionary->getMatchingQuery($queryText);
        $providerResults = $this->provider->geocodeQuery(GeocodeQuery::create($match));
        $results = $results->merge($providerResults->all());

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