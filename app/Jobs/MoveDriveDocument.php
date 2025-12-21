<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\DocumentFolder;
use App\Services\DriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use RuntimeException;

class MoveDriveDocument implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $documentId,
        public ?int $destinationFolderId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(DriveService $driveService): void
    {
        $document = Document::query()
            ->with('folder')
            ->findOrFail($this->documentId);

        $destinationFolder = $this->resolveDestinationFolder($document);

        $driveService->move(
            $document->drive_file_id,
            $document->folder?->drive_folder_id,
            $destinationFolder->drive_folder_id
        );

        $document->update([
            'folder_id' => $destinationFolder->id,
        ]);
    }

    private function resolveDestinationFolder(Document $document): DocumentFolder
    {
        if ($this->destinationFolderId) {
            $folder = DocumentFolder::query()->findOrFail($this->destinationFolderId);

            if ($folder->project_id !== $document->project_id) {
                throw new RuntimeException('Destination folder does not belong to the document project.');
            }

            return $folder;
        }

        $rootFolder = DocumentFolder::query()
            ->where('project_id', $document->project_id)
            ->where('kind', 'root')
            ->first();

        if (! $rootFolder) {
            throw new RuntimeException('Project Drive folders are not ready yet.');
        }

        return $rootFolder;
    }
}
