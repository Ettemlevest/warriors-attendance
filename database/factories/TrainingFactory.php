<?php

use Faker\Generator as Faker;

$examples = [
    'names' => ['Warriors edzés', 'Warriors tábor', 'Futó edzés', 'Spartan felkészítő'],
    'places' => ['Sportcsarnok', 'Focipálya', 'Horgos Rezidencia', 'Telki'],
    'times' => [
        0 => ['start_at' => '18:00:00', 'end_at' => '18:45:00'],
        1 => ['start_at' => '19:00:00', 'end_at' => '19:45:00'],
        2 => ['start_at' => '18:45:00', 'end_at' => '19:45:00'],
        3 => ['start_at' => '20:00:00', 'end_at' => '21:00:00'],
        4 => ['start_at' => '09:00:00', 'end_at' => '11:00:00'],
    ],
];

$factory->define(App\Training::class, function (Faker $faker) use ($examples) {
    $date = $faker->dateTimeBetween('now', '+7 days')->format('Y-m-d');
    $time = $faker->randomElement($examples['times']);

    return [
        'name' => $faker->randomElement($examples['names']),
        'place' => $faker->randomElement($examples['places']),
        'start_at' => $date.' '.$time['start_at'],
        'end_at' => $date.' '.$time['end_at'],
        'max_attendees' => $faker->numberBetween(10, 32),
    ];
});
