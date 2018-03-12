<?php

namespace GeoLV;

use Illuminate\Database\Eloquent\Collection;

class AddressCollection extends Collection
{
    public function insideLocality(): AddressCollection
    {
        return $this->filter(function (Address $address) {
            return $address->match_locality > 0
                && $address->levenshtein_match_locality >= 70;
        });
    }

    public function outsideLocality(): AddressCollection
    {
        return $this->filter(function (Address $address) {
            return $address->match_locality == 0
                && $address->levenshtein_match_locality < 70;
        });
    }

    public function inMainCluster(): AddressCollection
    {
        return $this->filter(function (Address $address) {
            return $address->group == 1;
        });
    }

    public function calculateDispersion(): float
    {
        if ($this->count() == 0)
            return 0;

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

        return $dispersion;
    }
}