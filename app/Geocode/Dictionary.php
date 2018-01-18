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

        $reader = Reader::createFromPath(storage_path('app/dictionary.csv'), 'r');
        foreach ($reader as $match)
            $this->collection->put(mb_strtolower($match[1]), mb_strtolower($match[0]));
    }

    public function get($word)
    {
        return $this->collection->get($word, null);
    }

    public function getMatchingQuery($queryText)
    {
        $queryText = mb_strtolower($queryText);
        $queryWords = explode(' ', $queryText);

        foreach ($queryWords as $word) {
            $match = $this->get($word);

            if ($match)
                $queryText = preg_replace('/\b' . $word . '\b/', $match, $queryText);
        }

        return $queryText;
    }

    public static function address($text)
    {
        return ucwords((new static())->getMatchingQuery($text));
    }
}