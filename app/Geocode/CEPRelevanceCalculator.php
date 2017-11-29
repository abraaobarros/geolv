<?php

namespace GeoLV\Geocode;


use GeoLV\Address;
use GeoLV\Search;

class CEPRelevanceCalculator extends RelevanceCalculator
{
    private $cep;

    /**
     * CEPRelevanceCalculator constructor.
     * @param Search $search
     * @internal param $cep
     */
    public function __construct(Search $search)
    {
        parent::__construct($search);
        $this->cep = preg_replace('/\D/', '', $this->searchText);
    }


    public function calculateRelevance(Address $address)
    {
        //Add points if is from search
        if ($address->search_id == $this->search->id)
            $address->relevance += 100;

        $address->relevance += $address->postal_code == $this->cep ? 100 : 0;

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