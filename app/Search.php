<?php

namespace GeoLV;

use Geocoder\Query\GeocodeQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Search
 * @package GeoLV
 * @method static Search|Model firstOrCreate(array $data)
 */
class Search extends Model
{
    protected $fillable = [
        'text',
        'locale',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * @param GeocodeQuery $query
     * @return Search|null
     */
    public static function findFromQuery(GeocodeQuery $query)
    {
        return static::whereText($query->getText())->first();
    }

    public static function exists(GeocodeQuery $query): bool
    {
        return static::findFromQuery($query) != null;
    }


}
