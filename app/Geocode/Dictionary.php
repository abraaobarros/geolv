<?php

namespace GeoLV\Geocode;


use League\Csv\Reader;

class Dictionary
{
    private $collection;

    /**
     * Dictionary constructor.
     */
    public function __construct()
    {
        $this->collection = collect();

        $reader = Reader::createFromPath(storage_path('app/dictionary.csv'), 'r')->setDelimiter(';');
        foreach ($reader as $match)
            $this->collection->put(strtolower($match[1]), strtolower($match[0]));
    }

    public function get($word) {
        return $this->collection->get($word, null);
    }

    public function getMatchingQueries($queryText)
    {
        $queryText = strtolower($queryText);
        $queries = [$queryText];
        $queryWords = explode(' ', $queryText);

        foreach ($queryWords as $word) {
            $match = $this->get($word);

            if ($match)
                $queries[] = preg_replace('/\b' . $word . '\b/', $match, $queryText);
        }

        return array_unique($queries);
    }
}