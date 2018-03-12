<?php

namespace GeoLV\Geocode\Clusters;


use GeoLV\AddressCollection;

class ClusterByAverage
{
    public function apply(AddressCollection $results)
    {
        foreach ($results as $i => $a) {
            $avg = 0;

            foreach ($results as $j => $b) {
                if ($i != $j) {
                    $avg += pow($a->latitude - $b->latitude, 2);
                    $avg += pow($a->longitude - $b->longitude, 2);
                }
            }

            if ($results->count() > 1)
                $avg /= $results->count() - 1;
            else
                $avg = 0;

            $a->average_dist = $avg;
        }

        $tot_avg = $results->avg('average_dist');

        foreach ($results as $m) {
            $m->cluster = $this->getGroup($m->average_dist, $tot_avg);
        }
    }

    private function getGroup($avg, $minAvg)
    {
        if ($avg > $minAvg)
            return 1;
        else
            return 2;
    }
}