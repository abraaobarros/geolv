<?php namespace App\Providers;

/**
 * This file is part of the Geocoder Laravel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Mike Bronner <hello@genealabs.com>
 * @license    MIT License
 */

use App\Geocode\GeocoderProvider;
use Geocoder\Laravel\Facades\Geocoder;
use Illuminate\Support\ServiceProvider;

class GeocoderService extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->app->singleton('geocoder', function () {
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
