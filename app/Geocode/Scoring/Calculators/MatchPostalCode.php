<?php

namespace GeoLV\Geocode\Scoring\Calculators;


use GeoLV\Address;
use GeoLV\Search;

class MatchPostalCode extends SearchRelevanceCalculator
{
    public function calculate(Address $address): float
    {
        if (blank($this->search->postal_code))
            return 0;

        $addressPostalCode = $this->clearPostalCode($address->postal_code);
        $searchPostalCode = $this->clearPostalCode($this->search->postal_code);
        return $addressPostalCode == $searchPostalCode? 1 : 0;
    }

    public function getName(): string
    {
        return 'match_postal_code';
    }

}