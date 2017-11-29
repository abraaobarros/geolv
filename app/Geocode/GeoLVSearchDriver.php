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
        $formatted = $search->formatted_text;
        $size = strlen($formatted);

        $results = $this->searchDriver
            ->query($formatted)
            ->getQuery()
            ->leftJoin('searches', 'search_id', '=', 'searches.id')
            ->get()
            ->map(function ($address) use ($search, $formatted, $size) {

                //Add points if is from search
                if ($address->search_id == $search->id)
                    $address->relevance += 100;

                $address->relevance -= (int) (levenshtein($address->text, $formatted) / $size) * 10;
                $address->relevance -= (int) (levenshtein($address->street_name, $formatted) / $size) * 20;

                //If not same street number
                if (!empty($address->street_number))
                    $address->relevance += str_contains($search->text, $address->street_number)? 10: -17;

                //If not same locality
                if (!empty($address->locality))
                    $address->relevance += str_contains($search->text, $address->locality)? 5: -10;

                //If not same sub locality
                if (!empty($address->sub_locality))
                    $address->relevance += str_contains($search->text, $address->sub_locality)? 5: -10;

                if ($address->relevance > 0) {

                    //Add points if has all data
                    $address->relevance += empty($address->street_name)? 0: 5;
                    $address->relevance += empty($address->street_number)? 0: 5;
                    $address->relevance += empty($address->sub_locality)? 0: 5;
                    $address->relevance += empty($address->locality)? 0: 5;
                    $address->relevance += empty($address->country_name)? 0: 5;

                }

                return $address;
            })
            ->filter(function ($address) {
                return $address->relevance > 0;
            })
            ->sortByDesc('relevance')
            ->values();

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