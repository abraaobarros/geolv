<?php

namespace GeoLV\Geocode;


use GeoLV\Address;
use GeoLV\AddressCollection;
use GeoLV\Geocode\Scoring\AddressRelevanceCalculator;
use GeoLV\Geocode\Scoring\IRelevanceCalculator;
use GeoLV\Geocode\Scoring\PostalCodeRelevanceCalculator;
use GeoLV\Search;
use Illuminate\Support\Collection;

class SortByRelevance
{
    /**
     * @var IRelevanceCalculator
     */
    private $relevanceCalculator;

    /**
     * SortByRelevance constructor.
     * @param Search $search
     */
    public function __construct(Search $search)
    {
        $this->relevanceCalculator = $this->getRelevanceCalculator($search);
    }

    public function apply(AddressCollection $results): AddressCollection
    {
        return $results->sortByDesc(function (Address $address) {
            return $this->relevanceCalculator->calculate($address);
        })
            ->groupBy('id')
            ->map(function (Collection $addresses) {
                return $addresses->first();
            })
            ->values();
    }

    private function getRelevanceCalculator(Search $search): IRelevanceCalculator
    {
        if (blank($search->text))
            return new PostalCodeRelevanceCalculator($search);
        else
            return new AddressRelevanceCalculator($search);
    }

}