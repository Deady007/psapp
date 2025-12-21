<?php

use App\Models\Project;
use App\Models\ProjectRequirement;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

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

it('forbids requirement access for users without roles', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->get(route('projects.requirements.index', $project))
        ->assertForbidden();
});
