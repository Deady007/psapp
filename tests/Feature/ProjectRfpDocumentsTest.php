<?php

use App\Jobs\GenerateProjectRfpDocument;
use App\Models\Project;
use App\Models\ProjectRequirement;
use App\Models\RfpDocument;
use App\Models\User;
use App\Services\GeminiClient;
use App\Services\RfpDocumentBuilder;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RbacSeeder::class);
    $this->withoutMiddleware(ValidateCsrfToken::class);
});

function createRfpUser(): User
{
    $user = User::factory()->create();
    $user->assignRole('admin');

    return $user;
}

it('queues rfp generation for a project', function () {
    Bus::fake();

    $user = createRfpUser();
    $project = Project::factory()->create();
    ProjectRequirement::factory()->create(['project_id' => $project->id]);

    $this->actingAs($user)
        ->from(route('projects.requirements.index', $project))
        ->post(route('projects.requirements.rfp.store', $project))
        ->assertRedirect(route('projects.requirements.index', $project));

    $rfpDocument = RfpDocument::query()->first();

    expect($rfpDocument)->not->toBeNull()
        ->and($rfpDocument->status)->toBe('queued');

    Bus::assertDispatched(GenerateProjectRfpDocument::class, function (GenerateProjectRfpDocument $job) use ($rfpDocument) {
        return $job->rfpDocumentId === $rfpDocument->id;
    });
});

it('requires requirements before queueing rfp generation', function () {
    Bus::fake();

    $user = createRfpUser();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->from(route('projects.requirements.index', $project))
        ->post(route('projects.requirements.rfp.store', $project))
        ->assertRedirect(route('projects.requirements.index', $project))
        ->assertSessionHas('error');

    expect(RfpDocument::query()->count())->toBe(0);

    Bus::assertNotDispatched(GenerateProjectRfpDocument::class);
});

it('downloads completed rfp documents', function () {
    Storage::fake('local');

    $user = createRfpUser();
    $project = Project::factory()->create();
    $filePath = 'rfp-documents/'.$project->id.'/rfp-sample.docx';

    Storage::disk('local')->put($filePath, 'example');

    $rfpDocument = RfpDocument::factory()->create([
        'project_id' => $project->id,
        'requested_by' => $user->id,
        'status' => 'completed',
        'file_name' => 'rfp-sample.docx',
        'file_path' => $filePath,
    ]);

    $this->actingAs($user)
        ->get(route('projects.requirements.rfp.download', [$project, $rfpDocument]))
        ->assertDownload('rfp-sample.docx');
});

it('builds rfp documents in the queue job', function () {
    Storage::fake('local');

    $user = createRfpUser();
    $project = Project::factory()->create();
    ProjectRequirement::factory()->create(['project_id' => $project->id]);

    $rfpDocument = RfpDocument::factory()->create([
        'project_id' => $project->id,
        'requested_by' => $user->id,
        'status' => 'queued',
    ]);

    config()->set('services.gemini.key', 'test-key');
    config()->set('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta');

    $payload = [
        'introduction' => [
            'purpose' => 'Define project requirements.',
            'scope' => 'Cover web and mobile features.',
            'overview' => 'Cloud hosted solution.',
        ],
        'system_overview' => 'The system includes web and mobile modules.',
        'non_functional' => [
            'performance' => 'Support 50 concurrent users.',
            'security' => 'Follow OWASP guidelines.',
            'availability' => 'Target 99.9% uptime.',
            'compliance' => 'N/A.',
        ],
        'technical_requirements' => 'N/A.',
        'user_interface' => [
            'UI line 1',
            'UI line 2',
            'UI line 3',
            'UI line 4',
            'UI line 5',
            'UI line 6',
            'UI line 7',
            'UI line 8',
        ],
        'data_requirements' => [
            'storage' => 'Use a relational database.',
            'backup' => 'Weekly backups.',
            'data_privacy' => 'Data stays within the client organization.',
        ],
        'assumptions' => [
            'Assumption 1',
            'Assumption 2',
            'Assumption 3',
        ],
        'acceptance_criteria' => [
            ['criterion' => 'Access roles enforced', 'validation_method' => 'UAT'],
            ['criterion' => 'Mobile screens render', 'validation_method' => 'UAT'],
            ['criterion' => 'Exports generated', 'validation_method' => 'UAT'],
        ],
        'appendices' => [
            'Glossary entry',
            'Supporting document',
            'System diagram',
        ],
    ];

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => json_encode($payload)],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $job = new GenerateProjectRfpDocument($rfpDocument->id);
    $job->handle(app(GeminiClient::class), app(RfpDocumentBuilder::class));

    $rfpDocument->refresh();

    expect($rfpDocument->status)->toBe('completed')
        ->and($rfpDocument->file_path)->not->toBeNull();

    Storage::disk('local')->assertExists($rfpDocument->file_path);
});

it('parses rfp sections when gemini returns raw newlines', function () {
    config()->set('services.gemini.key', 'test-key');
    config()->set('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta');
    app()->forgetInstance(GeminiClient::class);

    $payload = [
        'introduction' => [
            'purpose' => "Line 1\nLine 2",
            'scope' => 'Scope statement.',
            'overview' => 'Overview statement.',
        ],
        'system_overview' => 'System overview.',
        'non_functional' => [
            'performance' => 'Performance target.',
            'security' => 'Security baseline.',
            'availability' => 'Availability target.',
            'compliance' => 'Compliance note.',
        ],
        'technical_requirements' => 'Technical notes.',
        'user_interface' => [
            'UI line 1',
            'UI line 2',
            'UI line 3',
            'UI line 4',
            'UI line 5',
            'UI line 6',
            'UI line 7',
            'UI line 8',
        ],
        'data_requirements' => [
            'storage' => 'Storage details.',
            'backup' => 'Backup details.',
            'data_privacy' => 'Privacy details.',
        ],
        'assumptions' => [
            'Assumption 1',
            'Assumption 2',
            'Assumption 3',
        ],
        'acceptance_criteria' => [
            ['criterion' => 'Criterion 1', 'validation_method' => 'UAT'],
            ['criterion' => 'Criterion 2', 'validation_method' => 'UAT'],
            ['criterion' => 'Criterion 3', 'validation_method' => 'UAT'],
        ],
        'appendices' => [
            'Appendix 1',
            'Appendix 2',
            'Appendix 3',
        ],
    ];

    $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
    $invalidJson = str_replace('\\n', "\n", $json ?: '');

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => $invalidJson],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $requirements = [
        [
            'module_name' => 'Projects',
            'page_name' => 'Requirements',
            'title' => 'Generate RFP',
            'details' => 'Create a requirements document.',
            'priority' => 'high',
            'status' => 'todo',
        ],
    ];

    $sections = app(GeminiClient::class)->generateRfpSections($requirements);

    expect($sections['introduction']['purpose'])->toBe('Line 1 Line 2');
});
