<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $project = Project::query()->first() ?? Project::factory()->create();
        $folder = DocumentFolder::query()
            ->where('project_id', $project->id)
            ->where('kind', 'root')
            ->first()
            ?? DocumentFolder::factory()->create([
                'project_id' => $project->id,
                'kind' => 'root',
            ]);

        Document::factory()->create([
            'project_id' => $project->id,
            'folder_id' => $folder->id,
        ]);
    }
}
