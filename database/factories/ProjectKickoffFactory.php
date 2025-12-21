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
        $status = fake()->randomElement(ProjectKickoff::STATUSES);
        $plannedAt = fake()->dateTimeBetween('-1 week', '-1 day');
        $scheduledAt = in_array($status, ['scheduled', 'completed'], true)
            ? fake()->dateTimeBetween('now', '+1 week')
            : null;
        $meetingMode = $scheduledAt === null ? null : fake()->randomElement(['onsite', 'virtual_meet', 'virtual_teams']);
        $completedAt = $status === 'completed'
            ? fake()->dateTimeBetween('now', '+2 weeks')
            : null;

        return [
            'project_id' => Project::factory(),
            'purchase_order_number' => strtoupper(fake()->bothify('PO-####')),
            'planned_at' => $plannedAt,
            'scheduled_at' => $scheduledAt,
            'completed_at' => $completedAt,
            'meeting_mode' => $meetingMode,
            'site_location' => $meetingMode === 'onsite' ? fake()->address() : null,
            'meeting_link' => $meetingMode !== 'onsite' ? fake()->url() : null,
            'requirements_summary' => fake()->paragraph(),
            'timeline_summary' => fake()->sentence(),
            'notes' => fake()->paragraph(),
            'status' => $status,
        ];
    }
}
