<?php

namespace Database\Factories;

use App\Enums\ProjectStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'name'        => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status'      => fake()->randomElement(ProjectStatusEnum::cases()),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => ProjectStatusEnum::ACTIVE]);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => ProjectStatusEnum::COMPLETED]);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['status' => ProjectStatusEnum::ARCHIVED]);
    }
}
