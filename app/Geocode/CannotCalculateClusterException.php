<?php


namespace GeoLV\Geocode;


use Illuminate\Support\Collection;

class CannotCalculateClusterException extends \Exception
{
    /**
     * CannotCalculateClusterException constructor.
     * @param Collection $points
     * @param float $max_d
     */
    public function __construct(Collection $points, $max_d)
    {
        parent::__construct("Could not calculate cluster of points ($max_d): " . $points->toJson(JSON_PRETTY_PRINT));
    }
}