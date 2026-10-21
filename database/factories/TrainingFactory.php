<?php

namespace Database\Factories;

use App\Models\Training;
use App\Models\TrainingAttendance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Training>
 */
class TrainingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['easy', 'running', 'hard', 'other']),
            'name' => fake()->sentence(3),
            'place' => fake()->city(),
            'start_at' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'length' => fake()->randomElement([60, 90, 120]),
            'max_attendees' => fake()->numberBetween(10, 30),
            'can_attend_more' => false,
        ];
    }

    public function past(): static
    {
        return $this->state(fn () => [
            'start_at' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    public function unlimited(): static
    {
        return $this->state(fn () => [
            'max_attendees' => 0,
        ]);
    }

    public function full(): static
    {
        return $this
            ->state(fn () => [
                'max_attendees' => 1,
                'can_attend_more' => false,
            ])
            ->afterCreating(fn (Training $training) => TrainingAttendance::factory()->for($training)->create());
    }

    public function withOverflow(): static
    {
        return $this->state(fn () => [
            'can_attend_more' => true,
        ]);
    }
}
