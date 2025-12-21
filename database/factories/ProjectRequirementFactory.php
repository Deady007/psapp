<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectRequirement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectRequirement>
 */
class ProjectRequirementFactory extends Factory
{
    protected $model = ProjectRequirement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'module_name' => fake()->words(2, true),
            'page_name' => fake()->optional()->word(),
            'title' => fake()->sentence(4),
            'details' => fake()->paragraph(),
            'priority' => fake()->randomElement(ProjectRequirement::PRIORITIES),
            'status' => fake()->randomElement(ProjectRequirement::STATUSES),
        ];
    }
}
