<?php

namespace GeoLV;

use Illuminate\Database\Eloquent\Collection;
use Location\Distance\Vincenty;

class AddressCollection extends Collection
{
    public function insideLocality(): AddressCollection
    {
        return $this->filter(function (Address $address) {
            return $address->match_locality > 0
                && $address->levenshtein_match_locality >= 0.7;
        });
    }

    public function outsideLocality(): AddressCollection
    {
        return $this->filter(function (Address $address) {
            return $address->match_locality == 0
                && $address->levenshtein_match_locality < 0.7;
        });
    }

    public function inMainCluster(): AddressCollection
    {
        if ($this->count() == 0)
            return $this;

        $main_cluster = $this->first()->cluster;
        return $this->filter(function (Address $address) use ($main_cluster) {
            return $address->cluster == $main_cluster;
        });
    }

    public function getClustersCount(): int
    {
        return $this->groupBy('cluster')->count();
    }

    public function getProvidersCount(): int
    {
        return $this->groupBy('provider')->count();
    }

    public function calculateDispersion(): float
    {
        if ($this->count() <= 1)
            return -1;

        $dispersion = 0.0;
        $latMed = 0.0;
        $lngMed = 0.0;

        /** @var Address $address */
        foreach ($this as $address) {
            $latMed += $address->latitude;
            $lngMed += $address->longitude;
        }

        $latMed /= $this->count();
        $lngMed /= $this->count();

        /** @var Address $address */
        foreach ($this as $address) {
            $dispersion += pow(($address->latitude - $latMed) * 100, 2);
            $dispersion += pow(($address->longitude - $lngMed) * 100, 2);
        }

        return sqrt($dispersion) * 11057;
    }

    public function calculateAvgDistance(): float
    {
        $count = $this->count() - 1;
        if ($count <= 0)
            return 0;

        $first = $this->first();
        $coordinate = $first->coordinate;
        $calculator = new Vincenty();
        $sum = 0.0;

        foreach ($this as $address) {
            if ($address->id != $first->id) {
                $sum += $address->coordinate->getDistance($coordinate, $calculator);
            }
        }

        return $sum / $count;
    }

    public function calculatePrecision(): float
    {
        $count = $this->count() - 1;
        if ($count <= 0)
            return 0;

        /** @var Address $first */
        $first = $this->first();
        $coordinate = $first->coordinate;
        $calculator = new Vincenty();
        $distances = [];

        /** @var Address $address */
        foreach ($this as $address) {
            if ($address->id != $first->id) {
                $distances[] = $address->coordinate->getDistance($coordinate, $calculator);
            }
        }

        $avgDistance = array_sum($distances) / $count;
        $deviations = [];

        foreach ($distances as $distance) {
            $deviations[] = pow($distance - $avgDistance, 2);
        }

        return sqrt(array_sum($deviations) / $count);
    }

    public function calculateConfidence(): float
    {
        if ($this->count() <= 0)
            return 0;

        $first = $this->first();
        $levenshtein = $first->levenshtein_match_search_text;
        $clusters = 1.0 / $this->getClustersCount();
        $providers = $this->inMainCluster()->getProvidersCount() / 4.0;

        return ($levenshtein + $clusters + $providers) / 3.0;
    }
}