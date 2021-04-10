<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

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

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => 'secret',
        'remember_token' => Str::random(10),
        'owner' => false,
        'size' => $faker->boolean === true ? $faker->randomElement(['S', 'M', 'L', 'XL', 'XXL']) : null,
        'birth_date' => $faker->boolean === true ? $faker->dateTimeThisCentury->format('Y-m-d') : null,
        'address' => $faker->boolean === true ? $faker->address : null,
        'phone' => $faker->boolean === true ? $faker->phoneNumber : null,
    ];
});
