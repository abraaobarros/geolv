<?php

namespace GeoLV;

use Illuminate\Database\Eloquent\Model;
use Location\Coordinate;

/**
 * Class Locality
 * @package GeoLV\Geocode
 * @property int id
 * @property string name
 * @property string state
 * @property int state_id
 * @property string ibge_code
 * @property float min_lat
 * @property float min_lng
 * @property float max_lat
 * @property float max_lng
 */
class Locality extends Model
{
    protected $table = 'localities';
    protected $fillable = [
        'name',
        'state',
        'state_id',
        'ibge_code',
        'min_lat',
        'min_lng',
        'max_lat',
        'max_lng',
    ];

    protected $casts = [
        'id' => 'int',
        'state_id' => 'int'
    ];

    public function isInsideBounds(Coordinate $coordinate): bool
    {
        return $coordinate->getLat() >= $this->min_lat && $coordinate->getLat() <= $this->max_lat &&
            $coordinate->getLng() >= $this->min_lng && $coordinate->getLng() <= $this->max_lng;
    }

    public function getRect(): array
    {
        return [
            $this->min_lat,
            $this->min_lng,
            $this->max_lat,
            $this->max_lng
        ];
    }
}