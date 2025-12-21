<?php

namespace App\Jobs;

use App\Models\DocumentFolder;
use App\Models\Project;
use App\Services\DriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateProjectDriveFolders implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $projectId,
        public ?int $ownerId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(DriveService $driveService): void
    {
        $project = Project::query()->findOrFail($this->projectId);

        $rootExists = DocumentFolder::query()
            ->where('project_id', $project->id)
            ->where('kind', 'root')
            ->exists();

        if (! $rootExists) {
            $rootContainer = $driveService->createFolder(
                $this->projectContainerName($project),
                config('drive.root_folder_id')
            );

            $root = $driveService->createFolder('root', $rootContainer['id']);

            DocumentFolder::query()->create([
                'project_id' => $project->id,
                'parent_id' => null,
                'name' => $root['name'],
                'drive_folder_id' => $root['id'],
                'owner_id' => $this->ownerId,
                'kind' => 'root',
            ]);
        }

        $trashExists = DocumentFolder::query()
            ->where('project_id', $project->id)
            ->where('kind', 'trash')
            ->exists();

        if (! $trashExists) {
            $trashContainer = $driveService->createFolder(
                $this->projectContainerName($project),
                config('drive.trash_folder_id')
            );

            $trash = $driveService->createFolder('trash', $trashContainer['id']);

            DocumentFolder::query()->create([
                'project_id' => $project->id,
                'parent_id' => null,
                'name' => $trash['name'],
                'drive_folder_id' => $trash['id'],
                'owner_id' => $this->ownerId,
                'kind' => 'trash',
            ]);
        }
    }

    private function projectContainerName(Project $project): string
    {
        $identifier = $project->code ?: (string) $project->id;

        return $project->name.' ('.$identifier.')';
    }
}
