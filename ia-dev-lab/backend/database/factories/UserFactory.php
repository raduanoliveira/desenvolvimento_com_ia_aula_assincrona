<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'google_id' => fake()->unique()->numerify('google-########'),
            'github_id' => null,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }

    public function github(): static
    {
        return $this->state(fn (array $attributes) => [
            'google_id' => null,
            'github_id' => fake()->unique()->numerify('github-########'),
        ]);
    }
}
