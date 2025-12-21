<?php

namespace App\Jobs;

use App\Models\DocumentFolder;
use App\Services\DriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateDriveFolder implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $parentFolderId,
        public string $name,
        public ?int $ownerId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(DriveService $driveService): void
    {
        $parentFolder = DocumentFolder::query()->findOrFail($this->parentFolderId);

        $folder = $driveService->createFolder($this->name, $parentFolder->drive_folder_id);

        DocumentFolder::query()->create([
            'project_id' => $parentFolder->project_id,
            'parent_id' => $parentFolder->id,
            'name' => $folder['name'],
            'drive_folder_id' => $folder['id'],
            'owner_id' => $this->ownerId,
            'kind' => 'folder',
        ]);
    }
}
