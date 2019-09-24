<?php

namespace GeoLV;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package GeoLV
 * @property string name
 * @property string email
 * @property string password
 * @property \Illuminate\Support\Collection|GeocodingFile[] files
 * @property string role
 * @property int id
 * @property Carbon updated_at
 * @property Carbon email_verified_at
 * @property GoogleProvider googleMapsProvider
 * @property HereGeocoderProvider hereGeocoderProvider
 * @property BingMapsProvider bingMapsProvider
 * @property-read string google_maps_api_key
 * @property-read string here_geocoder_id
 * @property-read string here_geocoder_code
 * @property-read string bing_maps_api_key
 * @method static User create(array $array)
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    const ADMIN_ROLE = 'admin';
    const DEV_ROLE = 'dev';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'role'
    ];

    public function getGoogleMapsApiKeyAttribute()
    {
        return optional($this->googleMapsProvider)->api_key;
    }

    public function getHereGeocoderIdAttribute()
    {
        return optional($this->hereGeocoderProvider)->here_id;
    }

    public function getHereGeocoderCodeAttribute()
    {
        return optional($this->hereGeocoderProvider)->code;
    }

    public function getBingMapsApiKeyAttribute()
    {
        return optional($this->bingMapsProvider)->api_key;
    }

    public function getLastUpdateAttribute()
    {
        $last = $this->files()->withTrashed()->orderBy('updated_at', 'desc')->first();
        if (!empty($last))
            return $last->updated_at;
        else
            return $this->updated_at;
    }

    public function getTotalProcessedLinesAttribute()
    {
        return $this->files()->withTrashed()->sum('offset');
    }

    public function files()
    {
        return $this->hasMany(GeocodingFile::class);
    }

    public function googleMapsProvider()
    {
        return $this->hasOne(GoogleProvider::class);
    }

    public function hereGeocoderProvider()
    {
        return $this->hasOne(HereGeocoderProvider::class);
    }

    public function bingMapsProvider()
    {
        return $this->hasOne(BingMapsProvider::class);
    }

    public function isAdmin()
    {
        return ($this->role == static::ADMIN_ROLE) || $this->isDev();
    }

    public function isDev()
    {
        return $this->role == static::DEV_ROLE;
    }

    public function setProvider($provider, array $options)
    {
        $name = camel_case($provider) . "Provider";
        $provider = $this->{$name};

        if ($provider)
            $provider->update($options);
        else
            $this->{$name}()->create($options);
    }
}
