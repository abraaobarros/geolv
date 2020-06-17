<?php


namespace GeoLV\Geocode\Clusters;


use GeoLV\Geocode\GeoLVPythonService;
use Illuminate\Support\Collection;

class ClusterWithScipy
{
    private $python;

    /**
     * ClusterWithScipy constructor.
     */
    public function __construct()
    {
        $this->python = new GeoLVPythonService();
    }

    public function apply(Collection $collection, $max_d)
    {
        if ($collection->count() > 2) {
            $clusters = $this->python->getClusters($collection, $max_d);

            if (filled($clusters)) {
                foreach ($collection as $i => $address)
                    $address->cluster = $clusters[$i];

                return;
            }
        }

        foreach ($collection as $i => $address)
            $address->cluster = 1;
    }
}