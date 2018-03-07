<?php

namespace GeoLV\Geocode;


use GeoLV\AddressCollection;

class GroupByAverage
{
    const GROUP_NEAR = 'near';
    const GROUP_FAR = 'far';

    public function apply(AddressCollection $results): AddressCollection
    {
        foreach ($results as $i => $a) {
            $avg = 0;

            foreach ($results as $j => $b) {
                if ($i != $j) {
                    $avg += pow($a->latitude - $b->latitude, 2);
                    $avg += pow($a->longitude - $b->longitude, 2);
                }
            }

            $avg /= $results->count() - 1;
            $a->average_dist = $avg;
        }

        $tot_avg = $results->avg('average_dist');

        foreach ($results as $m) {
            $m->group = $this->getGroup($m->average_dist, $tot_avg);
        }

        return $results;
    }

    private function getGroup($avg, $minAvg)
    {
        if ($avg > $minAvg)
            return static::GROUP_FAR;
        else
            return static::GROUP_NEAR;
    }
}