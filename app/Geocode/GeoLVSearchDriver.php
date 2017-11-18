<?php

namespace GeoLV\Geocode;


use GeoLV\Address;
use GeoLV\Search;
use TomLingham\Searchy\Interfaces\SearchDriverInterface;
use TomLingham\Searchy\SearchDrivers\FuzzySearchDriver;

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
        $this->searchDriver = new FuzzySearchDriver('addresses', $this->searchColumns, $this->relevanceFieldName, ['addresses.*', 'text']);
    }

    public function query($searchString)
    {
        $search = Search::findFromText($searchString);
        $results = $this->searchDriver
            ->query($this->formatQueryText($searchString))
            ->getQuery()
            ->leftJoin('searches', 'search_id', '=', 'searches.id')
            ->get()
            ->map(function ($address) use ($search) {

                //Add points if is from search
                if ($address->search_id == $search->id)
                    $address->{$this->relevanceFieldName} += 100;

                //Remove points if search not match
                $address->{$this->relevanceFieldName} -= levenshtein($address->text, $search->text) * 25;

                //Add points if has all data
                $address->{$this->relevanceFieldName} += empty($address->street_name)? 0: 50;
                $address->{$this->relevanceFieldName} += empty($address->street_number)? 0: 50;
                $address->{$this->relevanceFieldName} += empty($address->locality)? 0: 50;
                $address->{$this->relevanceFieldName} += empty($address->sub_locality)? 0: 50;
                $address->{$this->relevanceFieldName} += empty($address->country_name)? 0: 50;


                return $address;
            })
            ->sortByDesc('relevance')
            ->values();

        $this->results = $results;
        return $this;
    }


    /**
     * @param $searchString
     * @return null|string|string[]
     */
    private function formatQueryText($searchString)
    {
        return preg_replace('/\s+/', ' ', str_replace(["-", ","], " ", $searchString));
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