<?php

namespace GeoLV\Geocode;


use GeoLV\Address;
use GeoLV\Search;
use Illuminate\Database\Eloquent\Collection;
use TomLingham\Searchy\Interfaces\SearchDriverInterface;
use TomLingham\Searchy\SearchDrivers\FuzzySearchDriver;

class GeoLVSearchDriver implements SearchDriverInterface
{
    private $results;
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
        'street_number',
        'postal_code',
        'text'
    ];

    /**
     * MatchQuerySearchDriver constructor.
     */
    public function __construct()
    {
        $this->results = collect();
        $this->searchDriver = new FuzzySearchDriver('addresses', $this->searchColumns, $this->relevanceFieldName, ['addresses.*', 'text']);
    }

    public function query($searchString)
    {
        $search = Search::findFromText($searchString);
        $relevanceCalculator = $this->getRelevanceCalculator($search);

        $this->results = $this->searchResults($search)
            ->map(function (Address $address) use ($relevanceCalculator) {
                $relevanceCalculator->calculateRelevance($address);
                return $address;
            })
            ->unique(function (Address $address) {
                return $address->getHashCode();
            })
            ->sortByDesc('relevance')
            ->values();

        return $this;
    }

    public function select(/* $columns */)
    {
        //
    }

    private function searchResults(Search $search): Collection
    {
        return Address::hydrate(
            $this->searchDriver
                ->query($search->formatted_text)
                ->getQuery()
                ->leftJoin('searches', 'search_id', '=', 'searches.id')
                ->get()
                ->toArray()
        );
    }

    public function get()
    {
        return $this->results;
    }

    private function getRelevanceCalculator(Search $search): IRelevanceCalculator
    {
        if (preg_match('/(\\d{5}\\s\\d{2})|\\d{7}/', $search->formatted_text))
            return new CEPRelevanceCalculator($search);
        else
            return new AddressRelevanceCalculator($search);
    }
}