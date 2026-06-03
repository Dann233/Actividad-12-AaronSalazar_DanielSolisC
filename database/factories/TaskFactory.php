<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'    => fake()->sentence(3),
            'user_id' => \App\Models\User::factory(),
            'status'  => \App\Enums\TaskStatus::Pendiente,
        ];
    }
}
