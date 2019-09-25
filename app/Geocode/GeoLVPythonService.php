<?php

namespace GeoLV\Geocode;


use GeoLV\GeocodingFile;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;

class GeoLVPythonService
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

    /**
     * @param GeocodingFile $file
     * @param $max_d
     * @return array|mixed
     */
    public function getFileClusters(GeocodingFile $file, $max_d)
    {
        try {
            $response = $this->client->request('POST', '/clusters/file', [
                'auth' => $this->auth,
                'json' => [
                    'path' => $file->output_path,
                    'header' => $file->header,
                    'sep' => $file->delimiter,

                    'indexes' => $file->indexes,
                    'fields' => $file->fields,

                    'max_d' => $max_d
                ],
            ]);

            return \GuzzleHttp\json_decode($response->getBody());
        } catch (GuzzleException $e) {
            report($e);
            return [];
        }
    }

    /**
     * @param GeocodingFile $file
     * @return array|mixed
     */
    public function getFilePoints(GeocodingFile $file)
    {
        try {
            $response = $this->client->request('POST', '/file/points', [
                'auth' => $this->auth,
                'json' => [
                    'path' => $file->output_path,
                    'header' => $file->header,
                    'sep' => $file->delimiter,

                    'indexes' => $file->indexes,
                    'fields' => $file->fields,
                ],
            ]);

            return \GuzzleHttp\json_decode($response->getBody());
        } catch (GuzzleException $e) {
            report($e);
            return [];
        }
    }

    /**
     * @param Collection $points
     * @param float $max_d
     * @return array
     */
    public function getClusters(Collection $points, float $max_d)
    {
        try {
            $points = $points->map(function ($point) {
                return [$point->latitude, $point->longitude];
            })->toArray();

            $response = $this->client->request('POST', '/clusters', [
                'auth' => $this->auth,
                'json' => [
                    'max_d' => $max_d,
                    'points' => $points
                ]
            ]);

            if (count($points) < 100)
                info("cluster ($max_d): " . json_encode($points,JSON_PRETTY_PRINT));

            return \GuzzleHttp\json_decode($response->getBody());
        } catch (GuzzleException $e) {
            report($e);
            return [];
        }
    }

}