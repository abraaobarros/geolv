<?php

namespace GeoLV;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GeocodingFile
 * @package GeoLV
 * @property-read int[] address_indexes
 * @property-read int[] locality_indexes
 * @property-read int[] postal_code_indexes
 * @property string path
 * @property string email
 * @property array indexes
 * @method static GeocodingFile|Model create($data)
 */
class GeocodingFile extends Model
{
    protected $fillable = [
        'path',
        'email',
        'offset',
        'indexes',
    ];

    protected $casts = [
        'indexes' => 'json'
    ];

    public function getAddressIndexesAttribute()
    {
        return $this->indexes['text'];
    }

    public function getLocalityIndexesAttribute()
    {
        return $this->indexes['locality'];
    }

    public function getPostalCodeIndexesAttribute()
    {
        return $this->indexes['postal_code'];
    }

}
