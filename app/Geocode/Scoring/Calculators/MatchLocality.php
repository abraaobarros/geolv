<?php

namespace GeoLV\Geocode\Scoring\Calculators;


use GeoLV\Address;
use GeoLV\Locality;

class MatchLocality extends SearchRelevanceCalculator
{
    public function calculate(Address $address): float
    {
        /** @var Locality $locality */
        $locality = $this->search->findLocality();

        if (blank($locality))
            return 1;

        return $locality->isInsideBounds($address->coordinate)? 1 : 0;
    }

    public function getName(): string
    {
        return 'match_locality';
    }

}