<?php

use Faker\Generator as Faker;

$examples = [
    'names' => ['Warriors edzés', 'Warriors tábor', 'Futó edzés', 'Spartan felkészítő'],
    'places' => ['Sportcsarnok', 'Focipálya', 'Horgos Rezidencia', 'Telki'],
    'times' => ['18:00:00', '19:00:00', '18:45:00', '20:00:00', '09:00:00'],
    'length' => [60, 90, 120],
];

$factory->define(App\Training::class, function (Faker $faker) use ($examples) {
    $date = $faker->dateTimeBetween('now', '+7 days')->format('Y-m-d');

    return [
        'name' => $faker->randomElement($examples['names']),
        'place' => $faker->randomElement($examples['places']),
        'start_at' => $date.' '.$faker->randomElement($examples['times']),
        'length' => $faker->randomElement($examples['length']),
        'max_attendees' => $faker->numberBetween(10, 32),
    ];
});
