<?php

use Faker\Generator as Faker;
use GeoLV\User;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(GeoLV\User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});


$factory->define(GeoLV\GeocodingFile::class, function (Faker $faker) {
    $count = rand(0, 100);
    return [
        'path' => str_random(10) . '.csv',
        'offset' => $count - rand(0, 50),
        'count' => $count,
        'delimiter' => ';',
        'done' => rand(0, 100) % 2 == 0,
        'header' => rand(0, 100) % 2 == 0
    ];
});