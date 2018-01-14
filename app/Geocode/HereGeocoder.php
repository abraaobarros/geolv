<?php
/**
 * Created by PhpStorm.
 * User: luizg
 * Date: 17/11/2017
 * Time: 22:59
 */

namespace GeoLV\Geocode;


use Geocoder\Collection;
use Geocoder\Exception\UnsupportedOperation;
use Geocoder\Http\Provider\AbstractHttpProvider;
use Geocoder\Location;
use Geocoder\Model\Address;
use Geocoder\Model\AddressBuilder;
use Geocoder\Model\AddressCollection;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use Http\Client\HttpClient;

class HereGeocoder extends AbstractHttpProvider implements Provider
{
    private $appId;
    private $appCode;
    private $host = 'https://geocoder.cit.api.here.com/6.2/';

    public function __construct(HttpClient $client, string $appId, string $appCode, string $country = null)
    {
        parent::__construct($client);
        $this->appId = $appId;
        $this->appCode = $appCode;
    }

    public function geocodeQuery(GeocodeQuery $query): Collection
    {
        $locations = [];
        $data = $this->query($query->getText());

        if (empty($data))
            return new AddressCollection([]);

        $views = $data['Response']['View'];

        foreach ($views as $view)
            foreach ($view['Result'] as $result)
                $locations[] = $this->resultToLocation($result);


        return new AddressCollection($locations);
    }


    /**
     * @param string $searchString
     * @return array
     */
    protected function query(string $searchString)
    {
        $uri = $this->buildRequestUri([
            'searchtext' => $searchString,
            'gen' => 9,
            'contry' => 'BRA',
            'app_id' => $this->appId,
            'app_code' => $this->appCode
        ]);
        $response = $this->getUrlContents($uri);
        return $this->parseResponse($response);
    }

    protected function parseResponse($response)
    {
        $content = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException(sprintf(
                "Failed to parse JSON response: %s",
                json_last_error_msg()
            ));
        }

        return $content;
    }

    public function reverseQuery(ReverseQuery $query): Collection
    {
        throw new UnsupportedOperation();
    }

    public function getName(): string
    {
        return 'here_geocoder';
    }

    private function resultToLocation(array $result): Location
    {
        $builder = new AddressBuilder($this->getName());
        $builder->setStreetName($result['Location']['Address']['Street'] ?? "");
        $builder->setStreetNumber($result['Location']['Address']['HouseNumber'] ?? "");
        $builder->setSubLocality($result['Location']['Address']['District'] ?? "");
        $builder->setLocality($result['Location']['Address']['City'] ?? "");
        $builder->setCountryCode($result['Location']['Address']['Country'] ?? "");
        $builder->setPostalCode($result['Location']['Address']['PostalCode'] ?? "");
        $builder->setCoordinates($result['Location']['DisplayPosition']['Latitude'], $result['Location']['DisplayPosition']['Longitude']);
        $builder->setCountry($result['Location']['Address']['Country'] ?? "");

        return $builder->build(Address::class);
    }

    private function buildRequestUri($data)
    {
        $uri = $this->host . "geocode.json?";

        foreach ($data as $key => $value) {
            $uri .= "{$key}={$value}&";
        }

        return substr($uri, 0, -1);
    }


}