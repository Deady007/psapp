<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectKickoff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectKickoff>
 */
class ProjectKickoffFactory extends Factory
{
    protected $model = ProjectKickoff::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'purchase_order_number' => strtoupper(fake()->bothify('PO-####')),
            'scheduled_at' => fake()->dateTimeBetween('-1 week', '+2 weeks'),
            'meeting_mode' => fake()->randomElement(['online', 'onsite']),
            'stakeholders' => fake()->sentence(),
            'requirements_summary' => fake()->paragraph(),
            'timeline_summary' => fake()->sentence(),
            'notes' => fake()->paragraph(),
            'status' => fake()->randomElement(ProjectKickoff::STATUSES),
        ];
    }
}
