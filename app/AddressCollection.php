<?php

namespace GeoLV;

use Illuminate\Database\Eloquent\Collection;

class AddressCollection extends Collection
{
    public function insideLocality()
    {
        return $this->filter(function (Address $address) {
            return $address->match_locality > 0;
        });
    }

    public function outsideLocality()
    {
        return $this->filter(function (Address $address) {
            return $address->match_locality == 0;
        });
    }

    public function calculateDispersion()
    {
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
            $dispersion += pow($address->latitude - $latMed, 2);
            $dispersion += pow($address->longitude - $lngMed, 2);
        }

        return $dispersion;
    }
}