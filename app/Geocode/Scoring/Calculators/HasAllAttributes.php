<?php

namespace GeoLV\Geocode\Scoring\Calculators;


use GeoLV\Address;
use GeoLV\Search;

class HasAllAttributes extends SearchRelevanceCalculator
{
    public function calculate(Address $address): float
    {
        $relevance = blank($address->street_name) ? 0 : 0.2;
        $relevance += blank($address->street_number) ? 0 : 0.2;
        $relevance += blank($address->sub_locality) ? 0 : 0.2;
        $relevance += blank($address->locality) ? 0 : 0.2;
        $relevance += blank($address->country_name) ? 0 : 0.2;

        return $relevance;
    }

    public function getName(): string
    {
        return 'has_all_attributes';
    }

}