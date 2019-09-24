<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;


$factory->define(GeoLV\GeocodingFile::class, function (Faker $faker) {
    $count = rand(0, 100);
    return [
        'name' => str_random(10) . '.csv',
        'path' => str_random(10) . '.csv',
        'offset' => $count - rand(0, $count / 2),
        'count' => $count,
        'delimiter' => ';',
        'done' => rand(0, 100) % 2 == 0,
        'header' => rand(0, 100) % 2 == 0
    ];
});