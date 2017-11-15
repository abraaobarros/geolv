<?php

namespace GeoLV;

use Illuminate\Database\Eloquent\Model;

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
}
