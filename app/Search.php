<?php

namespace GeoLV;

use Geocoder\Query\GeocodeQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Search
 * @package GeoLV
 * @method static Search|Model firstOrCreate(array $data)
 * @property int id
 * @property string text
 * @property string locality
 * @property string postal_code
 * @property string locale
 * @method static Builder geocodeQuery(GeocodeQuery $geocodeQuery)
 */
class Search extends Model
{
    protected $fillable = [
        'text',
        'locality',
        'postal_code',
    ];

    public function addresses(): BelongsToMany
    {
        return $this->belongsToMany(Address::class);
    }

}
