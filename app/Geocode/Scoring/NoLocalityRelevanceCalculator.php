<?php

namespace GeoLV\Geocode\Scoring;


use GeoLV\Geocode\Scoring\Calculators\ContainsToken;
use GeoLV\Geocode\Scoring\Calculators\HasAllAttributes;
use GeoLV\Geocode\Scoring\Calculators\LevenshteinMatchToken;
use GeoLV\Geocode\Scoring\Calculators\MatchPostalCode;
use GeoLV\Geocode\Scoring\Calculators\MatchLastSearch;
use GeoLV\Geocode\Scoring\Calculators\MatchLocality;
use GeoLV\Search;

class NoLocalityRelevanceCalculator extends RelevanceAggregator
{
    /**
     * AddressRelevanceCalculator constructor.
     * @param Search $search
     */
    public function __construct(Search $search)
    {
        parent::__construct([
            new MatchLastSearch($search),
            new LevenshteinMatchToken($search, 'search_text'),
            new LevenshteinMatchToken($search, 'street_name'),
            new ContainsToken($search, 'street_number'),
            new MatchPostalCode($search),
            new HasAllAttributes($search)
        ]);
    }

}