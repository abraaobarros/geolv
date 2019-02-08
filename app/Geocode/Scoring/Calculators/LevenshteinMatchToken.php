<?php

namespace GeoLV\Geocode\Scoring\Calculators;


use GeoLV\Address;
use GeoLV\Search;

class LevenshteinMatchToken extends SearchRelevanceCalculator
{
    private $token;
    private $searchFieldText;
    private $searchFieldLength;

    /**
     * LevenshteinMatchToken constructor.
     * @param Search $search
     * @param string $token
     * @param string $searchToken
     */
    public function __construct(Search $search, $token, $searchToken = 'text')
    {
        parent::__construct($search);
        $this->token = $token;
        $this->searchFieldText = $this->clear($this->search->{$searchToken});
        $this->searchFieldLength = strlen($this->searchFieldText);
    }

    public function calculate(Address $address): float
    {
        if (blank($address->{$this->token}))
            return 0;

        $addressField = $this->clear($address->{$this->token});
        $match = $this->searchFieldLength - levenshtein($addressField, $this->searchFieldText);

        return $match / $this->searchFieldLength;
    }

    public function getName(): string
    {
        return 'levenshtein_match_' . $this->token;
    }

}