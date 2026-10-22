<?php

namespace Database\Factories;

use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrainingAttendance>
 */
class TrainingAttendanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'training_id' => Training::factory(),
            'user_id' => User::factory(),
            'attended' => false,
            'extra' => false,
        ];
    }

    public function attended(): static
    {
        return $this->state(fn () => [
            'attended' => true,
        ]);
    }
}
