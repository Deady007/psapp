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

        $root = DocumentFolder::factory()->create([
            'project_id' => $project->id,
            'parent_id' => null,
            'name' => 'Project Root',
            'owner_id' => $owner->id,
            'kind' => 'root',
        ]);

        DocumentFolder::factory()->count(2)->create([
            'project_id' => $project->id,
            'parent_id' => $root->id,
            'owner_id' => $owner->id,
        ]);

        DocumentFolder::factory()->create([
            'project_id' => $project->id,
            'parent_id' => null,
            'name' => 'Project Trash',
            'owner_id' => $owner->id,
            'kind' => 'trash',
        ]);
    }
}
