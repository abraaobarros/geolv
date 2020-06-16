<?php

namespace GeoLV;

use Carbon\Carbon;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use \Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;

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
 * @property-read string google_maps_api_key
 * @property-read string bing_maps_api_key
 * @property string here_geocoder_api_key
 * @method static User create(array $array)
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, MustVerifyEmailTrait;

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

    public function getHereGeocoderApiKeyAttribute()
    {
        return optional($this->hereGeocoderProvider)->api_key;
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

    public function providers()
    {
        return $this->hasMany(GeocodingProvider::class);
    }

    public function googleMapsProvider()
    {
        return $this->hasOne(GeocodingProvider::class)->googleMaps();
    }

    public function hereGeocoderProvider()
    {
        return $this->hasOne(GeocodingProvider::class)->hereGeocoder();
    }

    public function bingMapsProvider()
    {
        return $this->hasOne(GeocodingProvider::class)->bingMaps();
    }

    public function isAdmin()
    {
        return ($this->role == static::ADMIN_ROLE) || $this->isDev();
    }

    public function isDev()
    {
        return $this->role == static::DEV_ROLE;
    }

    /**
     * @param $provider
     * @param $apiKey
     * @return GeocodingProvider|Model|null
     */
    public function provider($provider, $apiKey = null): ?GeocodingProvider
    {
        $providerRelationName = camel_case($provider) . "Provider";

        try {
            /** @var Model $providerModel */
            $providerModel = $this->{$providerRelationName};

            if (filled($apiKey)) {
                $credentials = ['api_key' => $apiKey];
                if (filled($providerModel) && $providerModel->exists) {
                    $providerModel->update($credentials);
                } else {
                    $providerModel = $this->providers()->create(
                        array_merge($credentials, ['provider' => $provider])
                    );
                }
            }

            return $providerModel;
        } catch (\Exception $e) {
            return null;
        }
    }
}
