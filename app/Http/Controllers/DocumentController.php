<?php

namespace App\Http\Controllers;

use App\Http\Requests\CopyDocumentRequest;
use App\Http\Requests\DeleteDocumentRequest;
use App\Http\Requests\MoveDocumentRequest;
use App\Http\Requests\RenameDocumentRequest;
use App\Http\Requests\StoreDocumentFolderRequest;
use App\Http\Requests\UploadDocumentRequest;
use App\Jobs\CopyDriveDocument;
use App\Jobs\CreateDriveFolder;
use App\Jobs\CreateProjectDriveFolders;
use App\Jobs\MoveDriveDocument;
use App\Jobs\RenameDriveDocument;
use App\Jobs\TrashDriveDocument;
use App\Jobs\UploadDriveDocument;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:documents.view')->only(['index', 'show']);
        $this->middleware('permission:documents.create')->only(['store', 'copy']);
        $this->middleware('permission:documents.edit')->only(['rename', 'move']);
        $this->middleware('permission:documents.delete')->only(['destroy']);
    }

    public function index(Request $request, Project $project): View
    {
        $this->authorize('viewAny', Document::class);

        [$rootFolder, $trashFolder, $driveReady] = $this->ensureProjectDriveFolders($project, $request->user()?->id);

        return $this->renderIndex($request, $project, $rootFolder, null, $driveReady);
    }

    public function show(Request $request, Project $project, DocumentFolder $driveFolder): View
    {
        if ($driveFolder->kind === 'trash') {
            abort(404);
        }

        $this->authorize('view', $driveFolder);

        [$rootFolder, $trashFolder, $driveReady] = $this->ensureProjectDriveFolders($project, $request->user()?->id);

        return $this->renderIndex($request, $project, $rootFolder, $driveFolder, $driveReady);
    }

    public function store(UploadDocumentRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('create', Document::class);

        [$rootFolder, $trashFolder, $driveReady] = $this->ensureProjectDriveFolders($project, $request->user()?->id);

        if (! $driveReady || ! $rootFolder) {
            return back()->with('warning', 'Project Drive folders are being prepared. Please try again shortly.');
        }

        $folderId = $request->integer('folder_id');
        $folder = $folderId
            ? DocumentFolder::query()
                ->where('project_id', $project->id)
                ->findOrFail($folderId)
            : null;

        if ($folder) {
            $this->authorize('update', $folder);
        }

        $file = $request->file('file');
        $storedPath = $file->store('drive-uploads', 'local');

        UploadDriveDocument::dispatch(
            storedPath: $storedPath,
            originalName: $file->getClientOriginalName(),
            mimeType: $file->getClientMimeType(),
            source: $request->string('source')->toString(),
            receivedFrom: $request->string('received_from')->toString(),
            receivedAt: $request->date('received_at')?->toDateString(),
            projectId: $project->id,
            folderId: $folder?->id,
            uploadedBy: $request->user()->id,
        );

        return back()->with('success', 'Document upload queued.');
    }

    public function storeFolder(StoreDocumentFolderRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('create', DocumentFolder::class);

        [$rootFolder, $trashFolder, $driveReady] = $this->ensureProjectDriveFolders($project, $request->user()?->id);

        if (! $driveReady || ! $rootFolder) {
            return back()->with('warning', 'Project Drive folders are being prepared. Please try again shortly.');
        }

        $parentId = $request->integer('parent_id') ?: $rootFolder->id;

        $parentFolder = DocumentFolder::query()
            ->where('project_id', $project->id)
            ->findOrFail($parentId);

        if ($parentFolder->kind === 'trash') {
            return back()->withErrors(['parent_id' => 'Cannot create folders in the trash.']);
        }

        $this->authorize('update', $parentFolder);

        CreateDriveFolder::dispatch(
            parentFolderId: $parentFolder->id,
            name: $request->string('name')->toString(),
            ownerId: $request->user()?->id
        );

        return back()->with('success', 'Folder creation queued.');
    }

    public function rename(RenameDocumentRequest $request, Project $project, Document $driveDocument): RedirectResponse
    {
        $this->authorize('rename', $driveDocument);

        RenameDriveDocument::dispatch(
            documentId: $driveDocument->id,
            name: $request->string('name')->toString(),
        );

        return back()->with('success', 'Document rename queued.');
    }

    public function move(MoveDocumentRequest $request, Project $project, Document $driveDocument): RedirectResponse
    {
        $this->authorize('move', $driveDocument);

        $destinationFolderId = $request->integer('destination_folder_id');
        $destinationFolder = $destinationFolderId
            ? DocumentFolder::query()
                ->where('project_id', $project->id)
                ->findOrFail($destinationFolderId)
            : null;

        if ($destinationFolder) {
            $this->authorize('update', $destinationFolder);
        }

        MoveDriveDocument::dispatch(
            documentId: $driveDocument->id,
            destinationFolderId: $destinationFolder?->id,
        );

        return back()->with('success', 'Document move queued.');
    }

    public function copy(CopyDocumentRequest $request, Project $project, Document $driveDocument): RedirectResponse
    {
        $this->authorize('copy', $driveDocument);

        $destinationFolderId = $request->integer('destination_folder_id');
        $destinationFolder = $destinationFolderId
            ? DocumentFolder::query()
                ->where('project_id', $project->id)
                ->findOrFail($destinationFolderId)
            : null;

        if ($destinationFolder) {
            $this->authorize('update', $destinationFolder);
        }

        CopyDriveDocument::dispatch(
            documentId: $driveDocument->id,
            destinationFolderId: $destinationFolder?->id,
            name: $request->string('name')->toString() ?: null,
            uploadedBy: $request->user()->id,
        );

        return back()->with('success', 'Document copy queued.');
    }

    public function destroy(DeleteDocumentRequest $request, Project $project, Document $driveDocument): RedirectResponse
    {
        $this->authorize('delete', $driveDocument);

        [, $trashFolder, $driveReady] = $this->ensureProjectDriveFolders($project, $request->user()?->id);

        if (! $driveReady) {
            return back()->with('warning', 'Project Drive folders are being prepared. Please try again shortly.');
        }

        TrashDriveDocument::dispatch(documentId: $driveDocument->id);

        return back()->with('success', 'Document moved to trash.');
    }

    private function renderIndex(
        Request $request,
        Project $project,
        ?DocumentFolder $rootFolder,
        ?DocumentFolder $folder,
        bool $driveReady
    ): View {
        $foldersQuery = DocumentFolder::query()
            ->where('project_id', $project->id)
            ->where('kind', 'folder')
            ->with('parent')
            ->orderBy('name');

        if (! $request->user()->can('documents.view')) {
            $foldersQuery->where('owner_id', $request->user()->id);
        }

        $folders = $foldersQuery->get();
        $foldersByParent = $folders->groupBy('parent_id');

        $documentsQuery = Document::query()
            ->where('project_id', $project->id)
            ->with(['uploadedBy', 'folder']);

        $currentFolder = $folder ?? $rootFolder;

        if ($folder) {
            $documentsQuery->where('folder_id', $folder->id);
        } elseif ($rootFolder) {
            $documentsQuery->where('folder_id', $rootFolder->id);
        }

        $filters = [
            'source' => $request->string('source')->toString(),
            'received_from' => $request->string('received_from')->toString(),
            'mime_type' => $request->string('mime_type')->toString(),
            'received_at_from' => $request->string('received_at_from')->toString(),
            'received_at_to' => $request->string('received_at_to')->toString(),
        ];

        if ($filters['source'] !== '') {
            $documentsQuery->where('source', $filters['source']);
        }

        if ($filters['received_from'] !== '') {
            $documentsQuery->where('received_from', $filters['received_from']);
        }

        if ($filters['mime_type'] !== '') {
            $documentsQuery->where('mime_type', $filters['mime_type']);
        }

        if ($filters['received_at_from'] !== '') {
            $documentsQuery->whereDate('received_at', '>=', $filters['received_at_from']);
        }

        if ($filters['received_at_to'] !== '') {
            $documentsQuery->whereDate('received_at', '<=', $filters['received_at_to']);
        }

        $documents = $documentsQuery
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('projects.drive-documents.index', [
            'project' => $project,
            'folder' => $currentFolder,
            'rootFolder' => $rootFolder,
            'driveReady' => $driveReady,
            'documents' => $documents,
            'foldersByParent' => $foldersByParent,
            'folderOptions' => $rootFolder
                ? $this->flattenFolderOptions($foldersByParent, $rootFolder->id)
                : [],
            'breadcrumbs' => $this->buildBreadcrumbs($folder, $rootFolder),
            'filters' => $filters,
        ]);
    }

    /**
     * @param  Collection<int|null, Collection<int, DocumentFolder>>  $foldersByParent
     * @return array<int, string>
     */
    private function flattenFolderOptions(Collection $foldersByParent, ?int $parentId = null, string $prefix = ''): array
    {
        $options = [];

        foreach ($foldersByParent->get($parentId, collect()) as $folder) {
            $options[$folder->id] = $prefix.$folder->name;
            $options += $this->flattenFolderOptions($foldersByParent, $folder->id, $prefix.'-- ');
        }

        return $options;
    }

    /**
     * @return list<DocumentFolder>
     */
    private function buildBreadcrumbs(?DocumentFolder $folder, ?DocumentFolder $rootFolder): array
    {
        $breadcrumbs = [];

        while ($folder) {
            if ($rootFolder && $folder->is($rootFolder)) {
                break;
            }

            $folder->loadMissing('parent');
            $breadcrumbs[] = $folder;
            $folder = $folder->parent;
        }

        return array_reverse($breadcrumbs);
    }

    /**
     * @return array{DocumentFolder|null, DocumentFolder|null, bool}
     */
    private function ensureProjectDriveFolders(Project $project, ?int $ownerId): array
    {
        $rootFolder = DocumentFolder::query()
            ->where('project_id', $project->id)
            ->where('kind', 'root')
            ->first();

        $trashFolder = DocumentFolder::query()
            ->where('project_id', $project->id)
            ->where('kind', 'trash')
            ->first();

        $driveReady = $rootFolder !== null && $trashFolder !== null;

        if (! $driveReady) {
            CreateProjectDriveFolders::dispatch($project->id, $ownerId);
        }

        return [$rootFolder, $trashFolder, $driveReady];
    }
}
