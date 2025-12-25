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

        $existingCount = ProjectRequirement::query()
            ->where('project_id', $project->id)
            ->count();

        $remaining = 3 - $existingCount;

        if ($remaining > 0) {
            ProjectRequirement::factory()
                ->count($remaining)
                ->create(['project_id' => $project->id]);
        }
    }
}
