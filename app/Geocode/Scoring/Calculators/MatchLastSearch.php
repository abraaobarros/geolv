<?php

namespace GeoLV\Geocode\Scoring\Calculators;


use GeoLV\Address;
use GeoLV\Search;

class MatchLastSearch extends SearchRelevanceCalculator
{
    public function __construct(Search $search)
    {
        parent::__construct($search);
    }

    public function calculate(Address $address): float
    {
        if ($address->search_id == $this->search->id)
            return 1;
        else
            return 0;
    }

    public function getName(): string
    {
        return 'match_last_search';
    }

}