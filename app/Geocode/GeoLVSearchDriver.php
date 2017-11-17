<?php

namespace GeoLV\Geocode;


use TomLingham\Searchy\Interfaces\SearchDriverInterface;
use TomLingham\Searchy\SearchDrivers\FuzzySearchDriver;
use TomLingham\Searchy\SearchDrivers\LevenshteinSearchDriver;

class GeoLVSearchDriver implements SearchDriverInterface
{
    private $results;
    private $searchDriver;
    private $relevanceFieldName = 'relevance';
    private $searchColumns = [
        'street_name::street_number::sub_locality::locality::country_name',
        'street_name::street_number::sub_locality::locality',
        'street_name::street_number::country_name',
        'street_name::street_number::locality',
        'street_name::street_number::sub_locality',
        'street_name::street_number',
        'street_name',
        'street_number',
        'postal_code',
        'text'
    ];

    /**
     * MatchQuerySearchDriver constructor.
     */
    public function __construct()
    {
        $this->results = collect();
        $this->searchDriver = new FuzzySearchDriver('addresses', $this->searchColumns, $this->relevanceFieldName, ['addresses.*']);
    }

    public function query($searchString)
    {
        $results = $this->searchDriver->query($searchString)->getQuery()->leftJoin('searches', 'search_id', '=', 'searches.id')->get();
        $this->results = $results;
        return $this;
    }

    public function select(/* $columns */)
    {
        //
    }

    public function get()
    {
        return $this->results;
    }
}