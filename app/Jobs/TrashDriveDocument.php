<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\DocumentFolder;
use App\Services\DriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use RuntimeException;

class TrashDriveDocument implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $documentId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(DriveService $driveService): void
    {
        $document = Document::query()
            ->with('folder')
            ->findOrFail($this->documentId);

        $trashFolder = DocumentFolder::query()
            ->where('project_id', $document->project_id)
            ->where('kind', 'trash')
            ->first();

        if (! $trashFolder) {
            throw new RuntimeException('Project Drive trash folder is not ready yet.');
        }

        $driveService->move(
            $document->drive_file_id,
            $document->folder?->drive_folder_id,
            $trashFolder->drive_folder_id
        );

        $document->delete();

        $delayMinutes = config('drive.hard_delete_delay_minutes');

        if (is_numeric($delayMinutes)) {
            $delayMinutes = (int) $delayMinutes;

            $job = HardDeleteDriveDocument::dispatch($document->drive_file_id);

            if ($delayMinutes > 0) {
                $job->delay(now()->addMinutes($delayMinutes));
            }
        }
    }
}
