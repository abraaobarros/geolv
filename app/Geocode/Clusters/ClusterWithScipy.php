<?php

namespace GeoLV\Geocode\Clusters;


use GeoLV\Address;
use GeoLV\AddressCollection;
use GeoLV\Search;
use GuzzleHttp\Client;

class ClusterWithScipy
{
    private $client;
    private $search;

    public function __construct(Search $search)
    {
        $this->client = new Client(['base_uri' => 'http://apps.cepesp.io:89/']);
        $this->search = $search;
    }

    public function apply(AddressCollection $collection)
    {
        try {
            $clusters = $this->getClusters($collection, $this->search->max_d);
            foreach ($collection->values() as $i => $address)
                $address->cluster = $clusters[$i];
        } catch (\Exception $e) {
            foreach ($collection->values() as $i => $address)
                $address->cluster = 1;
        }
    }

    private function getClusters(AddressCollection $collection, float $max_d)
    {
        $points = $collection->map(function (Address $address) {
            return $address->latitude . ';' . $address->longitude;
        })->implode('|');

        $response = $this->client->request('GET', '/', [
            'query' => [
                'max_d' => $max_d,
                'points' => $points
            ]
        ]);

        return \GuzzleHttp\json_decode($response->getBody());
    }

}