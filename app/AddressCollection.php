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

    public function outsideMainCluster(): AddressCollection
    {
        if ($this->count() == 0)
            return $this;

        $main_cluster = $this->first()->cluster;
        return $this->filter(function (Address $address) use ($main_cluster) {
            return $address->cluster != $main_cluster;
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

    /**
     * Herfindahl-Hirschman Index (HHI)
     * @return float
     */
    public function calculateHHI(): float
    {
        $hhi = 0;
        $outsideMainCluster = $this->outsideMainCluster();
        $total = $outsideMainCluster->count();

        /** @var AddressCollection $cluster */
        foreach ($outsideMainCluster->groupBy('cluster') as $cluster) {
            $hhi += pow(($cluster->count() / $total), 2);
        };

        return $hhi;
    }

    public function calculateConfidence(): float
    {
        $mainLocation = $this->first();
        $locationsTotal = $this->count();
        $mainCluster = $this->inMainCluster();
        $mainClusterCount = $mainCluster->count();
        $outsideMainCluster = $this->outsideMainCluster();
        $outsideMainClusterCount = $outsideMainCluster->count();
        $mainLevenshtein = $mainLocation? $mainLocation->levenshtein_match_street_name * 100 : 0;
        $levenshteinAvg = $mainCluster->avg('levenshtein_match_street_name');
        $levenshteinOutsideAvg = $outsideMainCluster->avg('levenshtein_match_street_name');
        $providersCount = $this->getProvidersCount();
        $hhi = $this->calculateHHI();

        $confidence = 10
            - abs(3 - $mainClusterCount)
            - abs(4 - $providersCount)
            - abs(1 - ($levenshteinAvg - $levenshteinOutsideAvg))
            - abs($hhi)
            - abs((100 - $mainLevenshtein) * 0.05)
            - abs($locationsTotal > 0? $outsideMainClusterCount / $locationsTotal : 0);

        return min(10, max(0, $confidence));
    }

    public function getConfidenceInfo(): array
    {
        $mainLocation = $this->first();
        $locationsTotal = $this->count();
        $mainCluster = $this->inMainCluster();
        $mainClusterCount = $mainCluster->count();
        $outsideMainCluster = $this->outsideMainCluster();
        $outsideMainClusterCount = $outsideMainCluster->count();
        $mainLevenshtein = $mainLocation? $mainLocation->levenshtein_match_street_name * 100 : 0;
        $levenshteinAvg = $mainCluster->avg('levenshtein_match_street_name');
        $levenshteinOutsideAvg = $outsideMainCluster->avg('levenshtein_match_street_name');
        $providersCount = $this->getProvidersCount();
        $hhi = $this->calculateHHI();

        return [
            "3 - vector_cluster_locations_pry_first[1]" => 3 - $mainClusterCount,
            "4 - num_sources_pry" => 4 - $providersCount,
            "1 - (pry_cluster_avg_levenstein/100 - avg_other_cluster_levenstein/100)" => 1 - ($levenshteinAvg - $levenshteinOutsideAvg),
            "hhi" => $hhi,
            "(100 - pry_levenstein) * 0.05" => (100 - $mainLevenshtein) * 0.05,
            "1 - vector_cluster_locations_pry_first[1] / sum(vector_cluster_locations_pry_first)" => $locationsTotal > 0? $outsideMainClusterCount / $locationsTotal : 0,
        ];
    }
}