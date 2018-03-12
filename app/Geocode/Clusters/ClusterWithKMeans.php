<?php

namespace GeoLV\Geocode\Clusters;


use GeoLV\AddressCollection;
use KMeans\Cluster;
use KMeans\Point;
use KMeans\Space;

class ClusterWithKMeans
{
    /**
     * @var int
     */
    private $clusters;

    /**
     * GroupByCluster constructor.
     * @param int $clusters
     */
    public function __construct(int $clusters)
    {
        $this->clusters = $clusters;
    }

    public function apply(AddressCollection $results)
    {
        $space = new Space(2);

        foreach ($results as $i => $m) {
            $space->addPoint([$m->latitude, $m->longitude], ['key' => $i]);
        }

        /** @var Cluster $cluster */
        foreach ($space->solve($this->clusters) as $i => $cluster) {
            /** @var Point $point */
            foreach ($cluster as $point) {
                $p = $point->toArray();
                $results->get($p['data']['key'])->cluster = $i;
            }
        }
    }

}