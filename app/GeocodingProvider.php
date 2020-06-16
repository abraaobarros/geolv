<?php

namespace GeoLV;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GeocodingProvider
 * @package GeoLV
 * @property string provider
 * @property integer api_key
 */
class GeocodingProvider extends Model
{
    protected $fillable = [
        'provider',
        'api_key',
    ];

    const GOOGLE_MAPS = 'google_maps';
    const HERE_GEOCODER = 'here_geocoder';
    const BING_MAPS = 'bing_maps';

    public function scopeGoogleMaps(Builder $query)
    {
        return $query->where('provider', static::GOOGLE_MAPS);
    }

    public function scopeHereGeocoder(Builder $query)
    {
        return $query->where('provider', static::HERE_GEOCODER);
    }

    public function scopeBingMaps(Builder $query)
    {
        return $query->where('provider', static::BING_MAPS);
    }
}
