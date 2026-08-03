<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'name'       => fake()->sentence(4),
            'status'     => TaskStatus::Todo->value,
            'priority'   => 'medium',
            'project_id' => Project::factory(),
        ];
    }
}
