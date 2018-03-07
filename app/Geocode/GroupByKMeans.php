<?php

namespace GeoLV\Geocode;


use GeoLV\AddressCollection;
use KMeans\Cluster;
use KMeans\Point;
use KMeans\Space;

class GroupByKMeans
{
    /**
     * @var int
     */
    private $clusters;

    private $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    /**
     * GroupByCluster constructor.
     * @param int $clusters
     */
    public function __construct(int $clusters)
    {
        $this->clusters = $clusters;
    }

    public function apply(AddressCollection $results): AddressCollection
    {
        $space = new Space(2);

        foreach ($results as $i => $m) {
            $space->addPoint([$m->latitude, $m->longitude], ['key' => $i]);
        }

        /** @var Cluster $cluster */
        foreach ($space->solve($this->clusters) as $i => $cluster) {
            $group = substr($this->alphabet, $i, 1);
            /** @var Point $point */
            foreach ($cluster as $point) {
                $p = $point->toArray();
                $results->get($p['data']['key'])->group = $group;
            }
        }

        return $results;
    }

}