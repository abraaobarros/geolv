<?php namespace GeoLV\Providers;

/**
 * This file is part of the Geocoder Laravel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Mike Bronner <hello@genealabs.com>
 * @license    MIT License
 */

use GeoLV\Geocode\GeocoderProvider;
use Geocoder\Laravel\Facades\Geocoder;
use Illuminate\Contracts\Auth\Guard;
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
