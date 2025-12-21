<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectDocumentRequest;
use App\Http\Requests\UpdateProjectDocumentRequest;
use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:project_documents.view')->only(['index', 'download']);
        $this->middleware('permission:project_documents.create')->only(['create', 'store']);
        $this->middleware('permission:project_documents.edit')->only(['edit', 'update']);
        $this->middleware('permission:project_documents.delete')->only(['destroy']);
    }

    public function index(Project $project): View
    {
        $documents = $project->documents()
            ->with('uploadedBy')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('projects.documents.index', [
            'project' => $project,
            'documents' => $documents,
        ]);
    }

    public function create(Project $project): View
    {
        return view('projects.documents.create', [
            'project' => $project,
        ]);
    }

    public function store(StoreProjectDocumentRequest $request, Project $project): RedirectResponse|JsonResponse
    {
        $file = $request->file('file');
        $path = $file->store('project-documents/'.$project->id, 'local');
        $notes = $request->string('notes')->toString();
        $collectedAt = $request->date('collected_at');

        $project->documents()->create([
            'category' => $request->string('category')->toString(),
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'notes' => $notes !== '' ? $notes : null,
            'uploaded_by' => $request->user()?->id,
            'collected_at' => $collectedAt?->toDateString(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.documents.index', $project),
                'message' => 'Document uploaded.',
            ]);
        }

        return redirect()
            ->route('projects.documents.index', $project)
            ->with('success', 'Document uploaded.');
    }

    public function edit(Project $project, ProjectDocument $document): View
    {
        return view('projects.documents.edit', [
            'project' => $project,
            'document' => $document,
        ]);
    }

    public function update(UpdateProjectDocumentRequest $request, Project $project, ProjectDocument $document): RedirectResponse|JsonResponse
    {
        $notes = $request->string('notes')->toString();
        $collectedAt = $request->date('collected_at');
        $payload = [
            'category' => $request->string('category')->toString(),
            'notes' => $notes !== '' ? $notes : null,
            'collected_at' => $collectedAt?->toDateString(),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('project-documents/'.$project->id, 'local');
            Storage::disk('local')->delete($document->path);

            $payload = array_merge($payload, [
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        $document->update($payload);

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.documents.index', $project),
                'message' => 'Document updated.',
            ]);
        }

        return redirect()
            ->route('projects.documents.index', $project)
            ->with('success', 'Document updated.');
    }

    public function destroy(Project $project, ProjectDocument $document): RedirectResponse|JsonResponse
    {
        Storage::disk('local')->delete($document->path);
        $document->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.documents.index', $project),
                'message' => 'Document deleted.',
            ]);
        }

        return redirect()
            ->route('projects.documents.index', $project)
            ->with('success', 'Document deleted.');
    }

    public function download(Project $project, ProjectDocument $document): StreamedResponse
    {
        if (! Storage::disk('local')->exists($document->path)) {
            abort(404);
        }

        return Storage::disk('local')->download($document->path, $document->original_name);
    }
}
