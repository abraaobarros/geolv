<?php

namespace GeoLV\Geocode\Clusters;


use GeoLV\Address;
use GeoLV\AddressCollection;
use GeoLV\GeocodingFile;
use GeoLV\Search;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class ClusterWithScipy
{
    private $client;
    private $auth;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => config('services.cluster.url')]);
        $this->auth = [
            config('services.cluster.username'),
            config('services.cluster.password')
        ];
    }

    public function apply(Collection $collection, $max_d)
    {
        try {
            $clusters = $this->getClusters($collection, $max_d);
            foreach ($collection as $i => $address)
                $address->cluster = $clusters[$i];
        } catch (\Exception $e) {
            report($e);
            foreach ($collection as $i => $address)
                $address->cluster = 1;
        }
    }

    public function getResults(GeocodingFile $file)
    {
        try {
            dd([
                'path' => $file->output_path,
                'fields' => $file->fields,
                'header' => $file->header
            ]);

            $response = $this->client->request('GET', '/clusters/file', [
                'auth' => $this->auth,
                'query' => [
                    'path' => $file->output_path,
                    'fields' => $file->fields,
                    'header' => $file->header,
                    'sep' => $file->delimiter
                ],
            ]);

            return \GuzzleHttp\json_decode($response->getBody());
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getClusters(Collection $collection, float $max_d)
    {
        $points = $collection->map(function ($address) {
            return $address->latitude . ';' . $address->longitude;
        })->implode('|');

        $response = $this->client->request('POST', '/clusters', [
            'auth' => $this->auth,
            'json' => [
                'max_d' => $max_d,
                'points' => $points
            ]
        ]);

        return \GuzzleHttp\json_decode($response->getBody());
    }

}