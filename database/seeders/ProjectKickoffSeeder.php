<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectKickoff;
use Illuminate\Database\Seeder;

class ProjectKickoffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $project = Project::query()->first() ?? Project::factory()->create();

        if ($project->kickoff === null) {
            ProjectKickoff::factory()->create([
                'project_id' => $project->id,
            ]);
        }
    }
}
