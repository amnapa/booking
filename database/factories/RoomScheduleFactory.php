<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Room;
use App\RoomSchedule;
use Faker\Generator as Faker;

$factory->define(RoomSchedule::class, function (Faker $faker) {
    $startDate = $faker->dateTimeBetween('next Monday', 'next Monday +90 days');
    $endDate = $faker->dateTimeBetween($startDate, $startDate->format('Y-m-d H:i:s').' +' . rand(1, 14) . ' days');

    return [
        'room_id' => factory(Room::class)->create(),
        'start_date' => $startDate,
        'end_date' => $endDate,
        'price' => $faker->numberBetween(50,10000),
        'cancellation_penalty_percentage' => $faker->numberBetween(0,100),
    ];
});
