<?php

namespace Database\Factories;

use App\Models\ProjectKickoff;
use App\Models\ProjectKickoffStakeholder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectKickoffStakeholder>
 */
class ProjectKickoffStakeholderFactory extends Factory
{
    protected $model = ProjectKickoffStakeholder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_kickoff_id' => ProjectKickoff::factory(),
            'stakeholder_type' => User::class,
            'stakeholder_id' => User::factory(),
        ];
    }
}
