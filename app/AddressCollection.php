<?php

namespace GeoLV;

use Illuminate\Database\Eloquent\Collection;

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
}