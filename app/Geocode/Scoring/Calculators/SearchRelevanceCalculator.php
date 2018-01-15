<?php

namespace GeoLV\Geocode\Scoring\Calculators;


use GeoLV\Address;
use GeoLV\Geocode\Scoring\IRelevanceCalculator;
use GeoLV\Search;

abstract class SearchRelevanceCalculator implements IRelevanceCalculator
{
    protected $search;

    /**
     * SearchRelevanceCalculator constructor.
     * @param Search $search
     */
    public function __construct(Search $search)
    {
        $this->search = $search;
    }

    protected function clear($text)
    {
        return mb_strtolower(preg_replace('/\s+/', ' ', str_replace(["-", ","], " ", trim($text))));
    }

    protected function clearPostalCode($text)
    {
        return preg_replace('/\D/', '', $text);
    }

}