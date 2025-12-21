<?php

use App\Jobs\CreateProjectDriveFolders;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Project;
use App\Models\User;
use App\Services\DriveService;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\mock;

beforeEach(function () {
    $this->seed(RbacSeeder::class);
    $this->withoutMiddleware(ValidateCsrfToken::class);
});

function createDocumentUser(): User
{
    $user = User::factory()->create();
    $user->assignRole('admin');

    return $user;
}

/**
 * @return array{Project, DocumentFolder, DocumentFolder}
 */
function createProjectWithDriveFolders(?User $owner = null): array
{
    $project = Project::factory()->create();

    $rootFolder = DocumentFolder::factory()->create([
        'project_id' => $project->id,
        'parent_id' => null,
        'drive_folder_id' => 'root-folder-'.$project->id,
        'owner_id' => $owner?->id,
        'kind' => 'root',
    ]);

    $trashFolder = DocumentFolder::factory()->create([
        'project_id' => $project->id,
        'parent_id' => null,
        'drive_folder_id' => 'trash-folder-'.$project->id,
        'owner_id' => $owner?->id,
        'kind' => 'trash',
    ]);

    return [$project, $rootFolder, $trashFolder];
}

dataset('invalidDocumentUploadData', [
    'missing file' => [['file' => null], ['file']],
    'missing source' => [['source' => null], ['source']],
    'missing received from' => [['received_from' => null], ['received_from']],
]);

it('shows the drive document action buttons', function () {
    $user = createDocumentUser();
    [$project] = createProjectWithDriveFolders($user);

    $this->actingAs($user)
        ->get(route('projects.drive-documents.index', $project))
        ->assertSuccessful()
        ->assertSee('New Folder')
        ->assertSee('Upload File');
});

it('allows uploading a document', function () {
    Storage::fake('local');

    $user = createDocumentUser();
    [$project] = createProjectWithDriveFolders($user);
    $folder = DocumentFolder::factory()->create([
        'project_id' => $project->id,
        'drive_folder_id' => 'folder-123',
    ]);

    mock(DriveService::class, function ($mock) {
        $mock->shouldReceive('upload')
            ->once()
            ->andReturn([
                'id' => 'drive-file-123',
                'name' => 'proposal.pdf',
                'mime_type' => 'application/pdf',
                'size' => 12000,
                'checksum' => 'checksum-123',
            ]);
    });

    $payload = [
        'folder_id' => $folder->id,
        'source' => 'Email',
        'received_from' => 'Client',
        'received_at' => now()->toDateString(),
        'file' => UploadedFile::fake()->create('proposal.pdf', 20, 'application/pdf'),
    ];

    $this->actingAs($user)
        ->post(route('projects.drive-documents.upload', $project), $payload)
        ->assertRedirect();

    $document = Document::query()->first();

    expect($document)->not->toBeNull()
        ->and($document->drive_file_id)->toBe('drive-file-123')
        ->and($document->folder_id)->toBe($folder->id)
        ->and($document->project_id)->toBe($project->id)
        ->and($document->uploaded_by)->toBe($user->id);

    expect(Storage::disk('local')->allFiles('drive-uploads'))->toBeEmpty();
});

it('renames a document', function () {
    $user = createDocumentUser();
    [$project] = createProjectWithDriveFolders($user);
    $document = Document::factory()->create([
        'project_id' => $project->id,
        'drive_file_id' => 'drive-file-rename',
        'uploaded_by' => $user->id,
    ]);

    mock(DriveService::class, function ($mock) {
        $mock->shouldReceive('rename')
            ->once()
            ->with('drive-file-rename', 'Renamed.pdf')
            ->andReturn([
                'id' => 'drive-file-rename',
                'name' => 'Renamed.pdf',
            ]);
    });

    $this->actingAs($user)
        ->patch(route('projects.drive-documents.rename', [$project, $document]), [
            'name' => 'Renamed.pdf',
        ])
        ->assertRedirect();

    expect($document->refresh()->name)->toBe('Renamed.pdf');
});

it('moves a document', function () {
    $user = createDocumentUser();
    [$project] = createProjectWithDriveFolders($user);
    $fromFolder = DocumentFolder::factory()->create([
        'project_id' => $project->id,
        'drive_folder_id' => 'folder-from',
    ]);
    $toFolder = DocumentFolder::factory()->create([
        'project_id' => $project->id,
        'drive_folder_id' => 'folder-to',
    ]);
    $document = Document::factory()->create([
        'project_id' => $project->id,
        'drive_file_id' => 'drive-file-move',
        'folder_id' => $fromFolder->id,
        'uploaded_by' => $user->id,
    ]);

    mock(DriveService::class, function ($mock) use ($fromFolder, $toFolder) {
        $mock->shouldReceive('move')
            ->once()
            ->with('drive-file-move', $fromFolder->drive_folder_id, $toFolder->drive_folder_id);
    });

    $this->actingAs($user)
        ->patch(route('projects.drive-documents.move', [$project, $document]), [
            'destination_folder_id' => $toFolder->id,
        ])
        ->assertRedirect();

    expect($document->refresh()->folder_id)->toBe($toFolder->id);
});

it('copies a document', function () {
    $user = createDocumentUser();
    [$project] = createProjectWithDriveFolders($user);
    $sourceFolder = DocumentFolder::factory()->create([
        'project_id' => $project->id,
        'drive_folder_id' => 'folder-source',
    ]);
    $destinationFolder = DocumentFolder::factory()->create([
        'project_id' => $project->id,
        'drive_folder_id' => 'folder-destination',
    ]);
    $document = Document::factory()->create([
        'project_id' => $project->id,
        'drive_file_id' => 'drive-file-copy',
        'folder_id' => $sourceFolder->id,
        'uploaded_by' => $user->id,
        'version' => 2,
    ]);

    mock(DriveService::class, function ($mock) use ($destinationFolder) {
        $mock->shouldReceive('copy')
            ->once()
            ->with('drive-file-copy', 'Copy.pdf', $destinationFolder->drive_folder_id)
            ->andReturn([
                'id' => 'drive-file-copy-new',
                'name' => 'Copy.pdf',
                'mime_type' => 'application/pdf',
                'size' => 42000,
                'checksum' => 'checksum-copy',
            ]);
    });

    $this->actingAs($user)
        ->post(route('projects.drive-documents.copy', [$project, $document]), [
            'destination_folder_id' => $destinationFolder->id,
            'name' => 'Copy.pdf',
        ])
        ->assertRedirect();

    expect(Document::query()->count())->toBe(2);

    $copy = Document::query()->where('drive_file_id', 'drive-file-copy-new')->first();

    expect($copy)->not->toBeNull()
        ->and($copy->folder_id)->toBe($destinationFolder->id)
        ->and($copy->version)->toBe(3)
        ->and($copy->uploaded_by)->toBe($user->id);
});

it('moves a document to trash', function () {
    $user = createDocumentUser();
    [$project, , $trashFolder] = createProjectWithDriveFolders($user);
    $folder = DocumentFolder::factory()->create([
        'project_id' => $project->id,
        'drive_folder_id' => 'folder-trash-from',
    ]);
    $document = Document::factory()->create([
        'project_id' => $project->id,
        'drive_file_id' => 'drive-file-trash',
        'folder_id' => $folder->id,
        'uploaded_by' => $user->id,
    ]);

    mock(DriveService::class, function ($mock) use ($folder, $trashFolder) {
        $mock->shouldReceive('move')
            ->once()
            ->with('drive-file-trash', $folder->drive_folder_id, $trashFolder->drive_folder_id);
    });

    $this->actingAs($user)
        ->delete(route('projects.drive-documents.destroy', [$project, $document]))
        ->assertRedirect();

    $this->assertSoftDeleted('documents', [
        'id' => $document->id,
    ]);
});

it('validates document upload input', function (array $override, array $errors) {
    Storage::fake('local');

    $user = createDocumentUser();
    [$project] = createProjectWithDriveFolders($user);

    $payload = [
        'source' => 'Portal',
        'received_from' => 'Partner',
        'received_at' => now()->toDateString(),
        'file' => UploadedFile::fake()->create('upload.pdf', 40, 'application/pdf'),
    ];

    $payload = array_merge($payload, $override);

    if (array_key_exists('file', $override) && $override['file'] === null) {
        unset($payload['file']);
    }

    $this->actingAs($user)
        ->post(route('projects.drive-documents.upload', $project), $payload)
        ->assertSessionHasErrors($errors);
})->with('invalidDocumentUploadData');

it('creates a subfolder for a project', function () {
    $user = createDocumentUser();
    [$project, $rootFolder] = createProjectWithDriveFolders($user);

    mock(DriveService::class, function ($mock) use ($rootFolder) {
        $mock->shouldReceive('createFolder')
            ->once()
            ->with('Contracts', $rootFolder->drive_folder_id)
            ->andReturn([
                'id' => 'drive-folder-contracts',
                'name' => 'Contracts',
            ]);
    });

    $this->actingAs($user)
        ->post(route('projects.drive-documents.folders.store', $project), [
            'name' => 'Contracts',
            'parent_id' => $rootFolder->id,
        ])
        ->assertRedirect();

    $folder = DocumentFolder::query()->where('drive_folder_id', 'drive-folder-contracts')->first();

    expect($folder)->not->toBeNull()
        ->and($folder->project_id)->toBe($project->id)
        ->and($folder->parent_id)->toBe($rootFolder->id)
        ->and($folder->kind)->toBe('folder');
});

it('creates a project drive hierarchy', function () {
    $user = createDocumentUser();
    $project = Project::factory()->create([
        'name' => 'Project One',
        'code' => 'PRJ-001',
    ]);

    config([
        'drive.root_folder_id' => 'drive-root',
        'drive.trash_folder_id' => 'drive-trash',
    ]);

    mock(DriveService::class, function ($mock) {
        $mock->shouldReceive('createFolder')
            ->once()
            ->with('Project One (PRJ-001)', 'drive-root')
            ->andReturn([
                'id' => 'container-root',
                'name' => 'Project One (PRJ-001)',
            ]);
        $mock->shouldReceive('createFolder')
            ->once()
            ->with('root', 'container-root')
            ->andReturn([
                'id' => 'project-root',
                'name' => 'root',
            ]);
        $mock->shouldReceive('createFolder')
            ->once()
            ->with('Project One (PRJ-001)', 'drive-trash')
            ->andReturn([
                'id' => 'container-trash',
                'name' => 'Project One (PRJ-001)',
            ]);
        $mock->shouldReceive('createFolder')
            ->once()
            ->with('trash', 'container-trash')
            ->andReturn([
                'id' => 'project-trash',
                'name' => 'trash',
            ]);
    });

    $job = new CreateProjectDriveFolders($project->id, $user->id);
    $job->handle(app(DriveService::class));

    $root = DocumentFolder::query()
        ->where('project_id', $project->id)
        ->where('kind', 'root')
        ->first();

    $trash = DocumentFolder::query()
        ->where('project_id', $project->id)
        ->where('kind', 'trash')
        ->first();

    expect($root)->not->toBeNull()
        ->and($root->drive_folder_id)->toBe('project-root')
        ->and($root->name)->toBe('root');

    expect($trash)->not->toBeNull()
        ->and($trash->drive_folder_id)->toBe('project-trash')
        ->and($trash->name)->toBe('trash');
});
