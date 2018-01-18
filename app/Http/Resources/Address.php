<?php

namespace GeoLV\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class Address
 * @package GeoLV\Http\Resources
 * @mixin \GeoLV\Address
 */
class Address extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'street_name' => $this->street_name,
            'street_number' => $this->street_number,
            'locality' => $this->locality,
            'postal_code' => $this->postal_code,
            'sub_locality' => $this->sub_locality,
            'country_code' => $this->country_code,
            'country_name' => $this->country_name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'provider' => $this->provider,
            'relevance' => $this->algorithm
        ];
    }

}
