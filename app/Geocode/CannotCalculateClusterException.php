<?php


namespace GeoLV\Geocode;


class CannotCalculateClusterException extends \Exception
{
    /**
     * CannotCalculateClusterException constructor.
     * @param array $points
     * @param float $max_d
     */
    public function __construct(array $points, $max_d)
    {
        parent::__construct("Could not calculate cluster of points ($max_d): " . json_encode($points,JSON_PRETTY_PRINT));
    }
}