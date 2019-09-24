<?php

namespace Tests\Unit;

use GeoLV\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserHasProvidersTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    public function testGoogleMapsProvider()
    {
        $key = str_random(100);
        $provider = $this->user->provider('google_maps', ['api_key' => $key]);

        $this->assertNotNull($provider);
        $this->assertDatabaseHas('google_providers', $provider->toArray());

        $newKey = str_random(100);
        $provider = $this->user->provider('google_maps', ['api_key' => $newKey]);

        $this->assertNotNull($provider);
        $this->assertDatabaseHas('google_providers', $provider->toArray());
    }

    public function testHereGeocoderProvider()
    {
        $id = str_random(100);
        $code = str_random(100);
        $provider = $this->user->provider('here_geocoder', ['here_id' => $id, 'code' => $code]);

        $this->assertNotNull($provider);
        $this->assertDatabaseHas('here_geocoder_providers', $provider->toArray());

        $newId = str_random(100);
        $newCode = str_random(100);
        $provider = $this->user->provider('here_geocoder', ['here_id' => $newId, 'code' => $newCode]);

        $this->assertNotNull($provider);
        $this->assertDatabaseHas('here_geocoder_providers', $provider->toArray());
    }

    public function testBingMapsProvider()
    {
        $key = str_random(100);
        $provider = $this->user->provider('bing_maps', ['api_key' => $key]);

        $this->assertNotNull($provider);
        $this->assertDatabaseHas('bing_maps_providers', $provider->toArray());

        $newKey = str_random(100);
        $provider = $this->user->provider('bing_maps', ['api_key' => $newKey]);

        $this->assertNotNull($provider);
        $this->assertDatabaseHas('bing_maps_providers', $provider->toArray());
    }

    public function testEmptyProvider()
    {
        $provider = $this->user->provider('bing_maps', ['api_key' => null]);
        $this->assertNull($provider);
    }
}
