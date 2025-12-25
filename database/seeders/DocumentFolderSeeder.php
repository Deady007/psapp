<?php

namespace Database\Seeders;

use App\Models\DocumentFolder;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentFolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::query()->first() ?? User::factory()->create();

        $project = Project::query()->first() ?? Project::factory()->create();

        $root = DocumentFolder::query()
            ->where('project_id', $project->id)
            ->where('kind', 'root')
            ->first();

        if ($root === null) {
            $root = DocumentFolder::factory()->create([
                'project_id' => $project->id,
                'parent_id' => null,
                'name' => 'Project Root',
                'owner_id' => $owner->id,
                'kind' => 'root',
            ]);
        }

        $childCount = DocumentFolder::query()
            ->where('project_id', $project->id)
            ->where('parent_id', $root->id)
            ->count();

        if ($childCount < 2) {
            DocumentFolder::factory()->count(2 - $childCount)->create([
                'project_id' => $project->id,
                'parent_id' => $root->id,
                'owner_id' => $owner->id,
            ]);
        }

        $trash = DocumentFolder::query()
            ->where('project_id', $project->id)
            ->where('kind', 'trash')
            ->first();

        if ($trash === null) {
            DocumentFolder::factory()->create([
                'project_id' => $project->id,
                'parent_id' => null,
                'name' => 'Project Trash',
                'owner_id' => $owner->id,
                'kind' => 'trash',
            ]);
        }
    }
}
