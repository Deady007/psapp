<?php

use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

dataset('invalidDocumentData', [
    'missing category' => [['category' => null], ['category']],
    'missing file' => [['file' => null], ['file']],
]);

it('allows uploading a document', function () {
    Storage::fake('local');

    $user = createDocumentUser();
    $project = Project::factory()->create();

    $payload = [
        'category' => 'Job Card',
        'file' => UploadedFile::fake()->create('job-card.pdf', 120, 'application/pdf'),
        'notes' => 'Initial job card upload.',
        'collected_at' => now()->toDateString(),
    ];

    $this->actingAs($user)
        ->post(route('projects.documents.store', $project), $payload)
        ->assertRedirect(route('projects.documents.index', $project));

    $document = ProjectDocument::query()->first();

    expect($document)->not->toBeNull();
    Storage::disk('local')->assertExists($document->path);
});

it('allows updating a document and replacing its file', function () {
    Storage::fake('local');

    $user = createDocumentUser();
    $project = Project::factory()->create();
    $document = ProjectDocument::factory()->create([
        'project_id' => $project->id,
        'path' => 'project-documents/'.$project->id.'/old.txt',
        'original_name' => 'old.txt',
        'mime_type' => 'text/plain',
    ]);

    Storage::disk('local')->put($document->path, 'old file');

    $payload = [
        'category' => 'Analysis Report',
        'file' => UploadedFile::fake()->create('new-report.pdf', 90, 'application/pdf'),
        'notes' => 'Updated report file.',
        'collected_at' => now()->toDateString(),
    ];

    $this->actingAs($user)
        ->put(route('projects.documents.update', [$project, $document]), $payload)
        ->assertRedirect(route('projects.documents.index', $project));

    $document->refresh();

    Storage::disk('local')->assertMissing('project-documents/'.$project->id.'/old.txt');
    Storage::disk('local')->assertExists($document->path);
    expect($document->category)->toBe('Analysis Report');
});

it('allows deleting a document', function () {
    Storage::fake('local');

    $user = createDocumentUser();
    $project = Project::factory()->create();
    $document = ProjectDocument::factory()->create([
        'project_id' => $project->id,
        'path' => 'project-documents/'.$project->id.'/delete.txt',
        'original_name' => 'delete.txt',
        'mime_type' => 'text/plain',
    ]);

    Storage::disk('local')->put($document->path, 'delete file');

    $this->actingAs($user)
        ->delete(route('projects.documents.destroy', [$project, $document]))
        ->assertRedirect(route('projects.documents.index', $project));

    $this->assertSoftDeleted('project_documents', [
        'id' => $document->id,
    ]);
    Storage::disk('local')->assertMissing($document->path);
});

it('validates document input', function (array $override, array $errors) {
    Storage::fake('local');

    $user = createDocumentUser();
    $project = Project::factory()->create();

    $payload = [
        'category' => 'Test Request Form',
        'file' => UploadedFile::fake()->create('request.pdf', 40, 'application/pdf'),
        'notes' => 'Testing file.',
    ];

    $payload = array_merge($payload, $override);

    if (array_key_exists('file', $override) && $override['file'] === null) {
        unset($payload['file']);
    }

    $this->actingAs($user)
        ->post(route('projects.documents.store', $project), $payload)
        ->assertSessionHasErrors($errors);
})->with('invalidDocumentData');

it('forbids document access for users without roles', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->get(route('projects.documents.index', $project))
        ->assertForbidden();
});
