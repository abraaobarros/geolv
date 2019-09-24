<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(GeoLV\GoogleProvider::class, function (Faker $faker) {
    return [
        'api_key' => $faker->text(100),
    ];
});

$factory->define(GeoLV\BingMapsProvider::class, function (Faker $faker) {
    return [
        'api_key' => $faker->text(100),
    ];
});

$factory->define(GeoLV\HereGeocoderProvider::class, function (Faker $faker) {
    return [
        'here_id' => $faker->numerify('###########'),
        'here_code' => $faker->text(100),
    ];
});

