<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Room;
use App\Hotel;
use Faker\Generator as Faker;

$factory->define(Room::class, function (Faker $faker) {
    return [
        'hotel_id' => factory(Hotel::class)->create(),
        'name' => $faker->name,
        'type' => $faker->randomElement(['single','double','twin','triple','suite']),
        'price' => $faker->numberBetween(50,10000),
        'capacity' => $faker->numberBetween(1,7),
    ];
});
