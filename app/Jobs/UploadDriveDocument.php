<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\DocumentFolder;
use App\Services\DriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class UploadDriveDocument implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $storedPath,
        public string $originalName,
        public ?string $mimeType,
        public string $source,
        public string $receivedFrom,
        public ?string $receivedAt,
        public int $projectId,
        public ?int $folderId,
        public int $uploadedBy
    ) {}

    /**
     * Execute the job.
     */
    public function handle(DriveService $driveService): void
    {
        $disk = Storage::disk('local');

        if (! $disk->exists($this->storedPath)) {
            throw new RuntimeException('Upload source file is missing.');
        }

        $folder = $this->resolveFolder();

        $absolutePath = $disk->path($this->storedPath);

        $metadata = $driveService->upload(
            $absolutePath,
            $this->originalName,
            $this->mimeType,
            $folder?->drive_folder_id
        );

        $checksum = $metadata['checksum'] ?? hash_file('md5', $absolutePath);

        Document::query()->create([
            'project_id' => $this->projectId,
            'folder_id' => $folder?->id,
            'drive_file_id' => $metadata['id'],
            'name' => $metadata['name'] ?? $this->originalName,
            'mime_type' => $metadata['mime_type'] ?? $this->mimeType,
            'size' => $metadata['size'] ?? $disk->size($this->storedPath),
            'source' => $this->source,
            'received_from' => $this->receivedFrom,
            'received_at' => $this->receivedAt,
            'version' => 1,
            'checksum' => $checksum,
            'uploaded_by' => $this->uploadedBy,
        ]);

        $disk->delete($this->storedPath);
    }

    private function resolveFolder(): DocumentFolder
    {
        if ($this->folderId) {
            $folder = DocumentFolder::query()->findOrFail($this->folderId);

            if ($folder->project_id !== $this->projectId) {
                throw new RuntimeException('Folder does not belong to the selected project.');
            }

            return $folder;
        }

        $rootFolder = DocumentFolder::query()
            ->where('project_id', $this->projectId)
            ->where('kind', 'root')
            ->first();

        if (! $rootFolder) {
            throw new RuntimeException('Project Drive folders are not ready yet.');
        }

        return $rootFolder;
    }
}
