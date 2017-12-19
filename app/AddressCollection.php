<?php

namespace GeoLv;


use Geocoder\Model\Coordinates;
use Illuminate\Database\Eloquent\Collection;

class AddressCollection extends Collection
{
    /**
     * @var Coordinates|null
     */
    private $center;

    private function calculateCenter() {
        $total = $this->count();
        if ($total == 1)
            return $this->first();

        $x = 0.0;
        $y = 0.0;
        $z = 0.0;

        /** @var Address $address */
        foreach ($this as $address) {
            $x += $address->x;
            $y += $address->y;
            $z += $address->z;
        }

        $x = $x / $total;
        $y = $y / $total;
        $z = $z / $total;

        $centralLongitude = atan2($y, $x);
        $centralSquareRoot = sqrt($x * $x + $y * $y);
        $centralLatitude = atan2($z, $centralSquareRoot);

        return new Coordinates(rad2deg($centralLatitude), rad2deg($centralLongitude));
    }

    public function getCenter() {
        if (!$this->center)
            $this->center = $this->calculateCenter();

        return $this->center;
    }
}