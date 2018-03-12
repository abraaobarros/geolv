<?php

namespace GeoLV\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class AddressCollection
 * @package GeoLV\Http\Resources
 */
class AddressCollection extends ResourceCollection
{
    private $dispersion;

    public function __construct(\GeoLV\AddressCollection $collection)
    {
        parent::__construct($collection);
        $this->dispersion = $collection->insideLocality()->inMainCluster()->calculateDispersion();
    }

    public function with($request)
    {
        return [
            'dispersion' => $this->dispersion
        ];
    }

}
