<?php

namespace GeoLV\Geocode;


use GeoLV\Address;

class AddressRelevanceCalculator extends RelevanceCalculator
{
    public function calculateRelevance(Address $address)
    {
        //Add points if is from search
        if ($address->search_id == $this->search->id)
            $address->relevance += 100;

        $address->relevance -= (int) (levenshtein($address->text, $this->searchText) / $this->searchTextSize) * 10;
        $address->relevance -= (int) (levenshtein($address->street_name, $this->searchText) / $this->searchTextSize) * 20;

        //If not same street number
        if (!empty($address->street_number))
            $address->relevance += str_contains($this->searchText, $address->street_number)? 10: -17;

        //If not same locality
        if (!empty($address->locality))
            $address->relevance += str_contains($this->searchText, $address->locality)? 5: -10;

        //If not same sub locality
        if (!empty($address->sub_locality))
            $address->relevance += str_contains($this->searchText, $address->sub_locality)? 5: -10;

        if ($address->relevance > 0) {

            //Add points if has all data
            $address->relevance += empty($address->street_name)? 0: 5;
            $address->relevance += empty($address->street_number)? 0: 5;
            $address->relevance += empty($address->sub_locality)? 0: 5;
            $address->relevance += empty($address->locality)? 0: 5;
            $address->relevance += empty($address->country_name)? 0: 5;

        }
    }


}