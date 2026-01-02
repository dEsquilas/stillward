<?php

namespace Database\Factories;

use App\Enums\GoalCategory;
use App\Enums\GoalType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Goal>
 */
class GoalFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(GoalType::cases());

        return [
            'user_id' => User::factory(),
            'category' => fake()->randomElement(GoalCategory::cases()),
            'type' => $type,
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'target_value' => match ($type) {
                GoalType::Counter => fake()->numberBetween(10, 100),
                GoalType::Money => fake()->numberBetween(1000, 50000),
                GoalType::Percentage, GoalType::YesNo => null,
            },
            'current_value' => 0,
            'unit' => match ($type) {
                GoalType::Counter => fake()->randomElement(['books', 'km', 'sessions', 'hours']),
                default => null,
            },
            'currency' => match ($type) {
                GoalType::Money => 'EUR',
                default => null,
            },
            'is_completed' => false,
            'is_archived' => false,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
            'completed_at' => now(),
            'current_value' => $attributes['target_value'] ?? 100,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_archived' => true,
        ]);
    }
}
