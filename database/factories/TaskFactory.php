<?php

namespace Database\Factories;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id'  => Project::factory(),
            'title'       => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'priority'    => fake()->randomElement(TaskPriorityEnum::cases()),
            'status'      => fake()->randomElement(TaskStatusEnum::cases()),
            'due_date'    => fake()->optional()->dateTimeBetween('now', '+3 months')?->format('Y-m-d'),
        ];
    }

    public function todo(): static
    {
        return $this->state(fn () => ['status' => TaskStatusEnum::TODO]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => ['status' => TaskStatusEnum::IN_PROGRESS]);
    }

    public function done(): static
    {
        return $this->state(fn () => ['status' => TaskStatusEnum::DONE]);
    }

    public function highPriority(): static
    {
        return $this->state(fn () => ['priority' => TaskPriorityEnum::HIGH]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'due_date' => fake()->dateTimeBetween('-3 months', '-1 day')->format('Y-m-d'),
            'status'   => TaskStatusEnum::TODO,
        ]);
    }
}
