<?php

namespace GeoLV\Geocode\Scoring\Calculators;


use GeoLV\Address;
use GeoLV\Locality;
use GeoLV\Search;
use Location\Coordinate;
use Location\Polygon;

class MatchLocality extends SearchRelevanceCalculator
{
    public function calculate(Address $address): float
    {
        /** @var Locality $locality */
        $locality = Locality::whereName($this->search->locality)->first();

        if (blank($locality))
            return 1;

        return $locality->isInsideBounds($address->coordinate)? 1 : 0;
    }

    public function getName(): string
    {
        return 'match_locality';
    }

}