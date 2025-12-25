<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBoardDocumentRequest;
use App\Models\BoardDocument;
use App\Models\Project;
use App\Models\ProjectBoard;
use App\Services\KanbanDocumentBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KanbanBoardDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:kanban.edit')->only(['store']);
        $this->middleware('permission:kanban.view')->only(['download']);
    }

    public function store(
        StoreBoardDocumentRequest $request,
        Project $project,
        ProjectBoard $board,
        KanbanDocumentBuilder $builder
    ): RedirectResponse {
        $type = $request->validated()['type'];
        $document = $builder->build($board, $type);

        $path = sprintf(
            'kanban/%s/%s/%s',
            $project->id,
            Str::slug($board->type),
            $document['file_name']
        );

        Storage::disk('local')->put($path, $document['content']);

        $board->documents()->create([
            'type' => $type,
            'content' => $document['content'],
            'storage_path' => $path,
            'file_name' => $document['file_name'],
            'generated_by' => $request->user()?->id,
            'generated_at' => now(),
        ]);

        $message = $type === BoardDocument::TYPE_USER_MANUAL
            ? 'User manual generated.'
            : 'Validation report generated.';

        return redirect()
            ->route('projects.kanban.boards.show', [$project, $board])
            ->with('success', $message);
    }

    public function download(Project $project, ProjectBoard $board, BoardDocument $document): StreamedResponse|RedirectResponse
    {
        $disk = Storage::disk('local');

        if (! $disk->exists($document->storage_path)) {
            return back()->with('error', 'The document file is missing.');
        }

        return $disk->download($document->storage_path, $document->file_name);
    }
}
