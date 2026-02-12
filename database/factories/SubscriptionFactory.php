<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'plan_id' => Plan::factory(),
            'user_id' => User::factory(),
            'purchased_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'expired_at' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'expired_at' => now(),
        ]);
    }
}
