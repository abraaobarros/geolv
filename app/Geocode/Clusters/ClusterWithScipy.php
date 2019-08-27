<?php

namespace GeoLV\Geocode\Clusters;


use GeoLV\Address;
use GeoLV\AddressCollection;
use GeoLV\Search;
use GuzzleHttp\Client;

class ClusterWithScipy
{
    private $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => config('services.cluster.url')]);
    }

    public function apply(AddressCollection $collection, $max_d)
    {
        try {
            $clusters = $this->getClusters($collection, $max_d);
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

        $response = $this->client->request('GET', '/clusters', [
            'query' => [
                'max_d' => $max_d,
                'points' => $points
            ]
        ]);

        return \GuzzleHttp\json_decode($response->getBody());
    }

}