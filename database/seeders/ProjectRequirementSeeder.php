<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectRequirement;
use Illuminate\Database\Seeder;

class ProjectRequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $project = Project::query()->first() ?? Project::factory()->create();

        ProjectRequirement::factory()
            ->count(3)
            ->create(['project_id' => $project->id]);
    }
}
