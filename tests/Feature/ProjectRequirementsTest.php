<?php

use App\Models\Project;
use App\Models\ProjectKickoff;
use App\Models\ProjectRequirement;
use App\Models\User;
use App\Services\GeminiClient;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\mock;

beforeEach(function () {
    $this->seed(RbacSeeder::class);
    $this->withoutMiddleware(ValidateCsrfToken::class);
});

function createRequirementUser(): User
{
    $user = User::factory()->create();
    $user->assignRole('admin');

    return $user;
}

dataset('invalidRequirementData', [
    'missing module name' => [['module_name' => null], ['module_name']],
    'missing title' => [['title' => null], ['title']],
    'invalid priority' => [['priority' => 'urgent'], ['priority']],
    'invalid status' => [['status' => 'blocked'], ['status']],
]);

dataset('invalidRequirementBatchData', [
    'missing module name' => [[
        'requirements' => [[
            'module_name' => null,
            'page_name' => 'Dashboard',
            'title' => 'Display widgets',
            'details' => 'Show summary metrics.',
            'priority' => ProjectRequirement::PRIORITIES[0],
            'status' => ProjectRequirement::STATUSES[0],
        ]],
    ], ['requirements.0.module_name']],
    'invalid status' => [[
        'requirements' => [[
            'module_name' => 'Operations',
            'page_name' => 'Tasks',
            'title' => 'Track tasks',
            'details' => 'Track task status and owner.',
            'priority' => ProjectRequirement::PRIORITIES[0],
            'status' => 'blocked',
        ]],
    ], ['requirements.0.status']],
]);

it('allows creating a requirement', function () {
    $user = createRequirementUser();
    $project = Project::factory()->create();

    $payload = [
        'module_name' => 'Analytics',
        'page_name' => 'Dashboard',
        'title' => 'Display KPI widgets',
        'details' => 'Show summary metrics and charts.',
        'priority' => ProjectRequirement::PRIORITIES[1],
        'status' => ProjectRequirement::STATUSES[0],
    ];

    $this->actingAs($user)
        ->post(route('projects.requirements.store', $project), $payload)
        ->assertRedirect(route('projects.requirements.index', $project));

    $this->assertDatabaseHas('project_requirements', [
        'project_id' => $project->id,
        'module_name' => 'Analytics',
        'title' => 'Display KPI widgets',
    ]);
});

it('allows creating multiple requirements at once', function () {
    $user = createRequirementUser();
    $project = Project::factory()->create();

    $payload = [
        'requirements' => [
            [
                'module_name' => 'Module 1',
                'page_name' => 'Dashboard',
                'title' => 'Display KPI widgets',
                'details' => 'Show summary metrics and charts.',
                'priority' => ProjectRequirement::PRIORITIES[1],
                'status' => ProjectRequirement::STATUSES[0],
            ],
            [
                'module_name' => 'Module 2',
                'page_name' => 'Reports',
                'title' => 'Export to PDF',
                'details' => null,
                'priority' => ProjectRequirement::PRIORITIES[2],
                'status' => ProjectRequirement::STATUSES[1],
            ],
        ],
    ];

    $this->actingAs($user)
        ->post(route('projects.requirements.store', $project), $payload)
        ->assertRedirect(route('projects.requirements.index', $project));

    expect(ProjectRequirement::query()->where('project_id', $project->id)->count())->toBe(2);

    $this->assertDatabaseHas('project_requirements', [
        'project_id' => $project->id,
        'module_name' => 'Module 1',
        'title' => 'Display KPI widgets',
    ]);
});

it('allows updating a requirement', function () {
    $user = createRequirementUser();
    $project = Project::factory()->create();
    $requirement = ProjectRequirement::factory()->create(['project_id' => $project->id]);

    $payload = [
        'module_name' => 'Workflows',
        'page_name' => 'Approval',
        'title' => 'Add approval actions',
        'details' => 'Support approve/reject and comments.',
        'priority' => ProjectRequirement::PRIORITIES[2],
        'status' => ProjectRequirement::STATUSES[1],
    ];

    $this->actingAs($user)
        ->put(route('projects.requirements.update', [$project, $requirement]), $payload)
        ->assertRedirect(route('projects.requirements.index', $project));

    expect($requirement->refresh()->title)->toBe('Add approval actions');
});

it('allows deleting a requirement', function () {
    $user = createRequirementUser();
    $project = Project::factory()->create();
    $requirement = ProjectRequirement::factory()->create(['project_id' => $project->id]);

    $this->actingAs($user)
        ->delete(route('projects.requirements.destroy', [$project, $requirement]))
        ->assertRedirect(route('projects.requirements.index', $project));

    $this->assertSoftDeleted('project_requirements', [
        'id' => $requirement->id,
    ]);
});

it('validates requirement input', function (array $override, array $errors) {
    $user = createRequirementUser();
    $project = Project::factory()->create();

    $payload = [
        'module_name' => 'Operations',
        'page_name' => 'Tasks',
        'title' => 'Track tasks',
        'details' => 'Track task status and owner.',
        'priority' => ProjectRequirement::PRIORITIES[0],
        'status' => ProjectRequirement::STATUSES[0],
    ];

    $this->actingAs($user)
        ->post(route('projects.requirements.store', $project), array_merge($payload, $override))
        ->assertSessionHasErrors($errors);
})->with('invalidRequirementData');

it('validates batch requirement input', function (array $payload, array $errors) {
    $user = createRequirementUser();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->post(route('projects.requirements.store', $project), $payload)
        ->assertSessionHasErrors($errors);
})->with('invalidRequirementBatchData');

it('validates transcript uploads', function () {
    $user = createRequirementUser();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->post(route('projects.requirements.import.preview', $project), [
            'transcript' => UploadedFile::fake()->create('transcript.pdf', 10, 'application/pdf'),
        ])
        ->assertSessionHasErrors(['transcript']);
});

it('logs transcript analysis errors in debug mode', function () {
    $user = createRequirementUser();
    $project = Project::factory()->create();

    config()->set('app.debug', true);

    mock(GeminiClient::class)
        ->shouldReceive('extractRequirementsFromTranscript')
        ->andThrow(new RuntimeException('Gemini service unavailable.'));

    $this->actingAs($user)
        ->from(route('projects.requirements.import', $project))
        ->post(route('projects.requirements.import.preview', $project), [
            'transcript' => UploadedFile::fake()->create('transcript.txt', 10, 'text/plain'),
        ])
        ->assertRedirect(route('projects.requirements.import', $project))
        ->assertSessionHas('error_details');

    $this->actingAs($user)
        ->get(route('projects.requirements.import', $project))
        ->assertSuccessful()
        ->assertSee('console.error', false);
});

it('parses requirements from a transcript using gemini', function () {
    $user = createRequirementUser();
    $project = Project::factory()->create();

    config()->set('services.gemini.key', 'test-key');
    config()->set('services.gemini.model', 'gemini-2.5-flash');
    config()->set('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta');

    $requirements = [[
        'module_name' => 'Module A',
        'page_name' => 'Dashboard',
        'title' => 'Track activity',
        'details' => 'Show summary metrics.',
        'priority' => 'high',
        'status' => 'todo',
    ]];

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => json_encode($requirements)],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $this->actingAs($user)
        ->post(route('projects.requirements.import.preview', $project), [
            'transcript' => UploadedFile::fake()->create('transcript.txt', 10, 'text/plain'),
        ])
        ->assertSuccessful()
        ->assertSee('Module A')
        ->assertSee('Track activity');
});

it('uses a single-pass extraction for larger transcripts within the single-pass limit', function () {
    $user = createRequirementUser();
    $project = Project::factory()->create();

    config()->set('services.gemini.key', 'test-key');
    config()->set('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta');
    config()->set('services.gemini.chunk_size', 12000);
    config()->set('services.gemini.single_pass_max_chars', 20000);
    config()->set('services.gemini.requirements_output_tokens', 4096);
    config()->set('services.gemini.merge_output_tokens', 4096);
    config()->set('services.gemini.heavy_output_tokens', 8192);
    app()->forgetInstance(GeminiClient::class);

    $requirements = [[
        'module_name' => 'Module A',
        'page_name' => 'Dashboard',
        'title' => 'Track activity',
        'details' => 'Show summary metrics.',
        'priority' => 'high',
        'status' => 'todo',
    ]];

    $transcript = str_repeat('Requirement detail. ', 700);

    Http::fake(function (Request $request) use ($requirements) {
        $payload = $request->data();

        expect($payload['generationConfig']['maxOutputTokens'])->toBe(4096);
        expect($payload['contents'][0]['parts'][0]['text'])
            ->toContain('Transcript:')
            ->not->toContain('Chunk:');

        return Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => json_encode($requirements)],
                        ],
                    ],
                ],
            ],
        ], 200);
    });

    $this->actingAs($user)
        ->post(route('projects.requirements.import.preview', $project), [
            'analysis_mode' => 'fast',
            'transcript' => UploadedFile::fake()->createWithContent('transcript.txt', $transcript),
        ])
        ->assertSuccessful()
        ->assertSee('Module A');

    Http::assertSentCount(1);
});

it('stores kickoff transcript when importing from kickoff', function () {
    $user = createRequirementUser();
    $project = Project::factory()->create();
    $kickoff = ProjectKickoff::factory()->create([
        'project_id' => $project->id,
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    Storage::fake('local');

    config()->set('services.gemini.key', 'test-key');
    config()->set('services.gemini.model', 'gemini-2.5-flash');
    config()->set('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta');

    $requirements = [[
        'module_name' => 'Module A',
        'page_name' => 'Dashboard',
        'title' => 'Track activity',
        'details' => 'Show summary metrics.',
        'priority' => 'high',
        'status' => 'todo',
    ]];

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => json_encode($requirements)],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $this->actingAs($user)
        ->post(route('projects.requirements.import.preview', $project), [
            'source' => 'kickoff',
            'transcript' => UploadedFile::fake()->create('transcript.txt', 10, 'text/plain'),
        ])
        ->assertSuccessful();

    $kickoff->refresh();

    expect($kickoff->transcript_path)->not->toBeNull()
        ->and($kickoff->transcript_uploaded_at)->not->toBeNull();

    Storage::disk('local')->assertExists($kickoff->transcript_path);
});

it('imports approved requirements in bulk', function () {
    $user = createRequirementUser();
    $project = Project::factory()->create();

    $payload = [
        'requirements' => [
            [
                'selected' => 1,
                'module_name' => 'Module A',
                'page_name' => 'Dashboard',
                'title' => 'Track activity',
                'details' => 'Show summary metrics.',
                'priority' => ProjectRequirement::PRIORITIES[0],
                'status' => ProjectRequirement::STATUSES[0],
            ],
            [
                'selected' => 1,
                'module_name' => 'Module B',
                'page_name' => 'Reports',
                'title' => 'Export reports',
                'details' => null,
                'priority' => ProjectRequirement::PRIORITIES[1],
                'status' => ProjectRequirement::STATUSES[1],
            ],
        ],
    ];

    $this->actingAs($user)
        ->post(route('projects.requirements.import.store', $project), $payload)
        ->assertRedirect(route('projects.requirements.index', $project));

    expect(ProjectRequirement::query()->where('project_id', $project->id)->count())->toBe(2);
});

it('shows requirements for the selected module', function () {
    $user = createRequirementUser();
    $project = Project::factory()->create();
    $firstRequirement = ProjectRequirement::factory()->create([
        'project_id' => $project->id,
        'module_name' => 'Module A',
        'title' => 'Track activity',
    ]);
    $secondRequirement = ProjectRequirement::factory()->create([
        'project_id' => $project->id,
        'module_name' => 'Module B',
        'title' => 'Export reports',
    ]);

    $this->actingAs($user)
        ->get(route('projects.requirements.index', [$project, 'module' => $firstRequirement->module_name]))
        ->assertSuccessful()
        ->assertSee($firstRequirement->title)
        ->assertDontSee($secondRequirement->title);
});

it('returns module requirements over ajax', function () {
    $user = createRequirementUser();
    $project = Project::factory()->create();
    $firstRequirement = ProjectRequirement::factory()->create([
        'project_id' => $project->id,
        'module_name' => 'Module A',
        'title' => 'Track activity',
    ]);
    $secondRequirement = ProjectRequirement::factory()->create([
        'project_id' => $project->id,
        'module_name' => 'Module B',
        'title' => 'Export reports',
    ]);

    $this->actingAs($user)
        ->get(route('projects.requirements.index', [$project, 'module' => $firstRequirement->module_name]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ])
        ->assertSuccessful()
        ->assertSee($firstRequirement->title)
        ->assertDontSee($secondRequirement->title);
});

it('forbids requirement access for users without roles', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->get(route('projects.requirements.index', $project))
        ->assertForbidden();
});
