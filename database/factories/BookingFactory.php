<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Booking;
use App\Room;
use Faker\Generator as Faker;

$factory->define(Booking::class, function (Faker $faker) {

    $startDate = $faker->dateTimeBetween('next Monday', 'next Monday +90 days');
    $endDate = $faker->dateTimeBetween($startDate, $startDate->format('Y-m-d H:i:s').' +' . rand(1, 14) . ' days');

    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'phone_number' => $faker->phoneNumber,
        'checkin_date' => $startDate,
        'checkout_date' => $endDate,
        'price' => $faker->numberBetween(50,10000),
        'reservation_code' => $faker->unique(true)->regexify('[A-Z0-9]{10}')
    ];
});
