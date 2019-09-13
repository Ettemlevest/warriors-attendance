<?php

use App\Training;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        factory(User::class)->create([
            'name' => 'John Doe',
            'nickname' => 'Johnny',
            'email' => 'johndoe@example.com',
            'owner' => true,
        ]);

        factory(User::class, 5)->create();

        factory(Training::class, 3)->create();
    }
}
