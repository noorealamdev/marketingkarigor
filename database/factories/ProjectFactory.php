<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name'      => fake()->catchPhrase(),
            'status'    => 'active',
            'priority'  => 'medium',
            'client_id' => Client::factory(),
        ];
    }
}
