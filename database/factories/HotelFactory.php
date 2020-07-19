<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Hotel;
use Faker\Generator as Faker;
use Illuminate\Support\Str;



$factory->define(Hotel::class, function (Faker $faker) {

    $title = $faker->unique(true)->sentence(10);
    $slug = Str::slug($title, '-');

    return [
        'name' => $faker->name,
        'slug' => $slug,
        'description' => $faker->text,
    ];
});
