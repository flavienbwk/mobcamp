<?php

use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;


$factory->define(\App\Publication::class, function (Faker $faker) {
    return [
        "ids" => $faker->userName,
        "description" => $faker->sentence,
        "geolocation" => $faker->sentence,
        "User_id" => 1
    ];
});