<?php

namespace GeoLV;

use Geocoder\Query\GeocodeQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Search
 * @package GeoLV
 * @property int id
 * @property string text
 * @property string locality
 * @property string postal_code
 * @property string locale
 * @property-read string address
 * @method static Builder geocodeQuery(GeocodeQuery $geocodeQuery)
 * @method static Search|Model firstOrCreate(array $data)
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

    public function getAddressAttribute()
    {
        return trim(implode(" ", [$this->text, $this->locality, $this->postal_code]));
    }

}
