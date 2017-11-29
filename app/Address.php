<?php

namespace GeoLV;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

/**
 * Class Address
 * @package GeoLV
 * @method static Search|Model firstOrCreate(array $data)
 * @property int $relevance
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

    public function getHashCode()
    {
        $data = array_except($this->attributesToArray(), ['id', 'relevance', 'text', 'created_at', 'updated_at', 'search_id']);
        return md5(json_encode($data));
    }
}
