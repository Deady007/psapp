<?php

namespace App\Jobs;

use App\Services\DriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HardDeleteDriveDocument implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $driveFileId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(DriveService $driveService): void
    {
        $driveService->delete($this->driveFileId);
    }
}
