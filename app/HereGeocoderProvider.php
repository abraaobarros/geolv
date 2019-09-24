<?php

namespace GeoLV;

use Illuminate\Database\Eloquent\Model;

class HereGeocoderProvider extends Model
{
    protected $fillable = [
        'here_id',
        'code'
    ];
}
