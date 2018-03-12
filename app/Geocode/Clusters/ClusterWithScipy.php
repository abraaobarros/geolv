<?php

namespace GeoLV\Geocode\Clusters;


use GeoLV\Address;
use GeoLV\AddressCollection;
use GuzzleHttp\Client;

class ClusterWithScipy
{
    private $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'http://cepesp.io:89/']);
    }

    public function apply(AddressCollection $collection)
    {
        $clusters = $this->getClusters($collection, 0.003);

        foreach ($collection->values() as $i => $address)
            $address->cluster = $clusters[$i];
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