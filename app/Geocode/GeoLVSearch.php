<?php

namespace GeoLV\Geocode;

use GeoLV\Address;
use GeoLV\AddressCollection;
use GeoLV\Geocode\Clusters\ClusterWithScipy;
use GeoLV\Search;
use TomLingham\Searchy\SearchDrivers\FuzzySearchDriver as SearchDriver;

class GeoLVSearch
{
    const MAX_RESULTS = 30;

    /**
     * @param Search $search
     * @return AddressCollection
     */
    public function search(Search $search): AddressCollection
    {
        $results = $this->searchResults($search);

        $sorter = new SortByRelevance($search);
        $results = $sorter->apply($results);

        $groupper = new ClusterWithScipy($search);
        //$groupper = new ClusterByAverage();
        //$groupper = new ClusterWithKMeans();
        $groupper->apply($results);

        return $results->values();
    }

    /**
     * @param Search $search
     * @return AddressCollection
     */
    private function searchResults(Search $search): AddressCollection
    {
        return Address::hydrate(
            $this->searchDriver->query($search->address)->get()->take(static::MAX_RESULTS)->toArray()
        );
    }

}