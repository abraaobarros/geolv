<?php

namespace GeoLV;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Location\Coordinate;

/**
 * Class Address
 * @package GeoLV
 * @method static Search|Model firstOrCreate(array $data)
 * @method static AddressCollection hydrate(array $toArray)
 * @property string street_name
 * @property string street_number
 * @property string postal_code
 * @property string locality
 * @property string sub_locality
 * @property string country_code
 * @property string country_name
 * @property string provider
 * @property int total_relevance
 * @property double latitude
 * @property double longitude
 * @property double rad_latitude
 * @property double rad_longitude
 * @property double x
 * @property double y
 * @property double z
 * @property int search_id
 * @property-read array algorithm
 * @property-read Coordinate coordinate
 * @property-read Search search
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

    protected $appends = ['state'];

    public function search(): BelongsTo
    {
        return $this->belongsTo(Search::class);
    }

    public function getStateAttribute()
    {
        return $this->search->state;
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

    /**
     * @return Model|Locality|null
     */
    public function findLocality()
    {
        return Locality::query()
            ->where('min_lat', '<=', $this->latitude)
            ->where('min_lng', '<=', $this->longitude)
            ->where('max_lat', '>=', $this->latitude)
            ->where('max_lng', '>=', $this->longitude)
            ->first();
    }

    public function getLocalityAttribute($value)
    {
        if (blank($value))
            return optional($this->findLocality())->name;
        else
            return $value;
    }

    public function getCoordinateAttribute()
    {
        return new Coordinate($this->latitude, $this->longitude);
    }

    public function getAlgorithmAttribute()
    {
        return array_only($this->toArray(), [
            'match_last_search',
            'levenshtein_match_text',
            'levenshtein_match_street_name',
            'levenshtein_match_locality',
            'contains_street_number',
            'contains_sub_locality',
            'match_postal_code',
            'has_all_attributes',
        ]);
    }

    public function getFieldsAttribute()
    {
        return array_only($this->toArray(), $this->fillable);
    }

    public function newCollection(array $models = [])
    {
        return new AddressCollection($models);
    }

}
