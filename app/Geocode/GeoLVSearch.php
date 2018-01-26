<?php

namespace GeoLV\Geocode;

use GeoLV\Address;
use GeoLV\AddressCollection;
use GeoLV\Geocode\Scoring\AddressRelevanceCalculator;
use GeoLV\Geocode\Scoring\IRelevanceCalculator;
use GeoLV\Geocode\Scoring\PostalCodeRelevanceCalculator;
use GeoLV\Search;
use Illuminate\Database\Eloquent\Collection;
use TomLingham\Searchy\SearchDrivers\FuzzySearchDriver;

class GeoLVSearch
{
    private $searchDriver;
    private $relevanceFieldName = 'relevance';
    private $searchColumns = [
        'street_name::street_number::sub_locality::locality::country_name',
        'street_name::street_number::sub_locality::locality',
        'street_name::street_number::country_name',
        'street_name::street_number::locality',
        'street_name::street_number::sub_locality',
        'street_name::street_number',
        'street_name',
        'postal_code',
        'search_text',
        'search_postal_code',
        'search_locality',
        'search_text::search_locality',
        'search_text::search_postal_code',
        'search_text::search_locality::search_postal_code',
    ];

    /**
     * MatchQuerySearchDriver constructor.
     */
    public function __construct()
    {
        $this->searchDriver = new FuzzySearchDriver('addresses_view', $this->searchColumns, $this->relevanceFieldName, ['*']);
    }

    /**
     * @param Search $search
     * @return AddressCollection
     */
    public function search(Search $search): AddressCollection
    {
        $relevanceCalculator = $this->getRelevanceCalculator($search);

        return $this->searchResults($search)
            ->sortByDesc(function (Address $address) use ($relevanceCalculator) {
                return $relevanceCalculator->calculate($address);
            })
            ->groupBy('id')
            ->map(function (Collection $addresses) {
                return $addresses->first();
            })
            ->values();
    }

    /**
     * @param Search $search
     * @return AddressCollection
     */
    private function searchResults(Search $search): AddressCollection
    {
        return Address::hydrate(
            $this->searchDriver->query($search->address)->get()->toArray()
        );
    }

    private function getRelevanceCalculator(Search $search): IRelevanceCalculator
    {
        if (blank($search->text))
            return new PostalCodeRelevanceCalculator($search);
        else
            return new AddressRelevanceCalculator($search);
    }

}