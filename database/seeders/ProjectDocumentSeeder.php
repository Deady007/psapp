<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProjectDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $project = Project::query()->first() ?? Project::factory()->create();
        $path = 'project-documents/'.$project->id.'/seeded.txt';

        Storage::disk('local')->put($path, 'Seeded document');

        ProjectDocument::factory()->create([
            'project_id' => $project->id,
            'original_name' => 'seeded.txt',
            'path' => $path,
            'mime_type' => 'text/plain',
            'size' => Storage::disk('local')->size($path),
        ]);
    }
}
