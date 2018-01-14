<?php

namespace GeoLV\Geocode\Scoring\Calculators;


use GeoLV\Address;
use GeoLV\Search;

class LevenshteinMatchToken extends SearchRelevanceCalculator
{
    private $token;

    public function __construct(Search $search, string $token)
    {
        parent::__construct($search);
        $this->token = $token;
    }

    public function calculate(Address $address): float
    {
        if (blank($address->{$this->token}))
            return 0;

        $addressField = $this->clear($address->{$this->token});
        $searchText = $this->clear($this->search->text);
        $size = strlen($searchText);
        $match = $size - levenshtein($addressField, $searchText);

        return $match / $size;
    }

    public function getName(): string
    {
        return 'levenshtein_match_' . $this->token;
    }

}