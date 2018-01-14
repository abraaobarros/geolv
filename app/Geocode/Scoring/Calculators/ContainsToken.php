<?php

namespace GeoLV\Geocode\Scoring\Calculators;


use GeoLV\Address;
use GeoLV\Search;

class ContainsToken extends SearchRelevanceCalculator
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
        $searchText = $this->clear(join(" ", [$this->search->text, $this->search->locality]));

        return str_contains($searchText, $addressField) ? 1 : 0;
    }

    public function getName(): string
    {
        return 'contains_' . $this->token;
    }

}