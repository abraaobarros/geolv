<?php
/**
 * Created by PhpStorm.
 * User: luizg
 * Date: 14/11/2017
 * Time: 20:52
 */

namespace GeoLV\Geocode;


use Geocoder\Collection;
use Geocoder\Exception\UnsupportedOperation;
use Geocoder\Location;
use Geocoder\Model\AddressCollection;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use GeoLV\Address;
use TomLingham\Searchy\Interfaces\SearchDriverInterface;

class SearchResults implements Provider
{
    private $searchDriver;
    private $provider;

    public function __construct(SearchDriverInterface $searchDriver, Provider $provider)
    {
        $this->searchDriver = $searchDriver;
        $this->provider = $provider;
    }

    /**
     * @param GeocodeQuery $query
     *
     * @return Collection
     *
     * @throws \Geocoder\Exception\Exception
     */
    public function geocodeQuery(GeocodeQuery $query): Collection
    {
        //$this->store($this->provider->geocodeQuery($query));
        $queryText = $this->formatQueryText($query);
        $results = $this->searchDriver->query($queryText)->get();

        return new AddressCollection($results->toArray());
    }

    /**
     * @param ReverseQuery $query
     *
     * @return Collection
     *
     * @throws \Geocoder\Exception\Exception
     */
    public function reverseQuery(ReverseQuery $query): Collection
    {
        throw new UnsupportedOperation();
    }

    /**
     * Returns the provider's name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'search';
    }

    private function store(Collection $results)
    {
        /** @var Location $result */
        foreach ($results as $result) {
            if (empty($result->getStreetName()))
                continue;

            Address::firstOrCreate([
                'street_name'   => $result->getStreetName(),
                'street_number' => $result->getStreetNumber(),
                'locality'      => $result->getLocality(),
                'postal_code'   => $result->getPostalCode(),
                'sub_locality'  => $result->getSubLocality(),
                'country_code'  => $result->getCountry()->getCode(),
                'country_name'  => $result->getCountry()->getName(),
                'latitude'      => $result->getCoordinates()->getLatitude(),
                'longitude'     => $result->getCoordinates()->getLongitude(),
                'provider'      => $result->getProvidedBy()
            ]);
        }

    }

    /**
     * @param GeocodeQuery $query
     * @return null|string|string[]
     */
    private function formatQueryText(GeocodeQuery $query)
    {
        return preg_replace('/\s+/', ' ', str_replace(["-", ","], " ", $query->getText()));
    }

}