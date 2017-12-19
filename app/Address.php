<?php

namespace GeoLV;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Address
 * @package GeoLV
 * @method static Search|Model firstOrCreate(array $data)
 * @property int $relevance
 * @property double latitude
 * @property double longitude
 * @property double rad_latitude
 * @property double rad_longitude
 * @property double x
 * @property double y
 * @property double z
 */
class Address extends Model
{
    protected $fillable = [
        'street_name',
        'street_number',
        'locality',
        'postal_code',
        'sub_locality',
        'country_code',
        'country_name',
        'latitude',
        'longitude',
        'provider'
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double'
    ];

    public function search(): BelongsTo
    {
        return $this->belongsTo(Search::class);
    }

    public function getPostalCodeAttribute($value)
    {
        return preg_replace('/\D/', '', $value);
    }

    public function getRadLatitudeAttribute()
    {
        return deg2rad($this->latitude);
    }

    public function getRadLongitudeAttribute()
    {
        return deg2rad($this->longitude);
    }

    public function getXAttribute()
    {
        return cos($this->rad_latitude) * cos($this->rad_longitude);
    }

    public function getYAttribute()
    {
        return cos($this->rad_latitude) * sin($this->rad_longitude);
    }

    public function getZAttribute()
    {
        return sin($this->rad_latitude);
    }

    public function getHashCode()
    {
        $data = array_except($this->attributesToArray(), ['id', 'relevance', 'text', 'created_at', 'updated_at', 'search_id']);
        return md5(json_encode($data));
    }

    public function newCollection(array $models = [])
    {
        return new AddressCollection($models);
    }


}
