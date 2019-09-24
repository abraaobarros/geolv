<?php

/** @var Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(GeoLV\User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'email_verified_at' => now(),
        'remember_token' => str_random(10),
    ];
});

