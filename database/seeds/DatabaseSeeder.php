<?php

use App\Models\Training;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        factory(User::class)->create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'secret',
            'owner' => true,
        ]);

        factory(User::class, 3)->create([
            'owner' => true,
        ]);

        factory(User::class, 50)->create();

        // generate some trainings
        factory(Training::class, 51)->create();

        // add some unlimited trainigns to collection
        factory(Training::class, 21)->states('unlimited')->create();

        // add some attendees to trainings
        Training::all()->each(function ($training) {
            $users = User::take(
                    rand(0, $training->max_attendees + 5)
                )
                ->inRandomOrder()
                ->get();

            $training->attendees()->attach($users->take($training->max_attendees));

            // handle extra attendees (over the limit)
            if ($training->max_attendees < $users->count()) {
                $training->attendees()->attach($users->slice($training->max_attendees), ['extra' => true]);
            }
        });
    }
}
