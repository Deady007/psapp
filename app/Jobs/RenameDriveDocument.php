<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\DriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RenameDriveDocument implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $documentId,
        public string $name
    ) {}

    /**
     * Execute the job.
     */
    public function handle(DriveService $driveService): void
    {
        $document = Document::query()->findOrFail($this->documentId);

        $driveService->rename($document->drive_file_id, $this->name);

        $document->update([
            'name' => $this->name,
        ]);
    }
}
