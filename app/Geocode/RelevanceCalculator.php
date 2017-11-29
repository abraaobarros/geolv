<?php

namespace GeoLV\Geocode;


use GeoLV\Search;

abstract class RelevanceCalculator implements IRelevanceCalculator
{
    protected $search;
    protected $searchText;
    protected $searchTextSize;

    /**
     * RelevanceCalculator constructor.
     * @param $search
     */
    public function __construct(Search $search)
    {
        $this->search = $search;
        $this->searchText = $search->formatted_text;
        $this->searchTextSize = strlen($this->searchText);
    }

}