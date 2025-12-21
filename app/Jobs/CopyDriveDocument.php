<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\DocumentFolder;
use App\Services\DriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use RuntimeException;

class CopyDriveDocument implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $documentId,
        public ?int $destinationFolderId,
        public ?string $name,
        public int $uploadedBy
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

        $copyName = $this->name ?: $document->name;

        $metadata = $driveService->copy(
            $document->drive_file_id,
            $copyName,
            $destinationFolder->drive_folder_id
        );

        Document::query()->create([
            'project_id' => $document->project_id,
            'folder_id' => $destinationFolder->id,
            'drive_file_id' => $metadata['id'],
            'name' => $metadata['name'] ?? $copyName,
            'mime_type' => $metadata['mime_type'] ?? $document->mime_type,
            'size' => $metadata['size'] ?? $document->size,
            'source' => $document->source,
            'received_from' => $document->received_from,
            'received_at' => $document->received_at?->toDateString(),
            'version' => $document->version + 1,
            'checksum' => $metadata['checksum'] ?? $document->checksum,
            'uploaded_by' => $this->uploadedBy,
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
