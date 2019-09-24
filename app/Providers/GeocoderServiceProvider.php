<?php namespace GeoLV\Providers;

use GeoLV\Geocode\GeocoderProvider;
use Geocoder\Laravel\Facades\Geocoder;
use Illuminate\Support\ServiceProvider;

class GeocoderServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->app->singleton('geocoder', function ($app) {
            return new GeocoderProvider();
        });
    }

    public function register()
    {
        $this->app->alias('Geocoder', Geocoder::class);
    }

    public function provides() : array
    {
        return ['geocoder'];
    }
}
