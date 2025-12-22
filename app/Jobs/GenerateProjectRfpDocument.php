<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\RfpDocument;
use App\Services\GeminiClient;
use App\Services\RfpDocumentBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class GenerateProjectRfpDocument implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $rfpDocumentId) {}

    /**
     * Execute the job.
     */
    public function handle(GeminiClient $client, RfpDocumentBuilder $builder): void
    {
        $rfpDocument = RfpDocument::query()->findOrFail($this->rfpDocumentId);

        try {
            $rfpDocument->update([
                'status' => 'processing',
                'started_at' => now(),
                'error_message' => null,
            ]);

            $project = Project::query()
                ->with(['customer', 'products'])
                ->findOrFail($rfpDocument->project_id);

            $requirements = $project->requirements()
                ->orderBy('created_at')
                ->get();

            if ($requirements->isEmpty()) {
                throw new RuntimeException('No requirements available for RFP generation.');
            }

            $payload = $requirements
                ->map(fn ($requirement) => [
                    'module_name' => $requirement->module_name,
                    'page_name' => $requirement->page_name,
                    'title' => $requirement->title,
                    'details' => $requirement->details,
                    'priority' => $requirement->priority,
                    'status' => $requirement->status,
                ])
                ->all();

            $timeline = $project->start_date?->toDateString();
            if ($timeline && $project->due_date) {
                $timeline .= ' - '.$project->due_date->toDateString();
            } elseif ($project->due_date) {
                $timeline = $project->due_date->toDateString();
            }

            $context = array_filter([
                'Project' => $project->name,
                'Customer' => $project->customer?->name,
                'Description' => $project->description,
                'Products' => $project->products->pluck('name')->implode(', '),
                'Timeline' => $timeline ?: null,
            ], fn ($value) => is_string($value) && trim($value) !== '');

            $sections = $client->generateRfpSections($payload, $context);

            $fileName = sprintf(
                'rfp-%s-%s.docx',
                $project->id,
                now()->format('YmdHis')
            );

            $relativePath = sprintf('rfp-documents/%s/%s', $project->id, $fileName);

            $builder->build($project, $requirements->all(), $sections, $relativePath);

            $rfpDocument->update([
                'status' => 'completed',
                'file_name' => $fileName,
                'file_path' => $relativePath,
                'completed_at' => now(),
            ]);
        } catch (Throwable $exception) {
            $rfpDocument->update([
                'status' => 'failed',
                'failed_at' => now(),
                'error_message' => Str::limit($exception->getMessage(), 1000),
            ]);

            report($exception);

            throw $exception;
        }
    }
}
