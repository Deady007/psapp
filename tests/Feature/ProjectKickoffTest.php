<?php

use App\Models\Project;
use App\Models\ProjectKickoff;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

beforeEach(function () {
    $this->seed(RbacSeeder::class);
    $this->withoutMiddleware(ValidateCsrfToken::class);
});

function createKickoffUser(): User
{
    $user = User::factory()->create();
    $user->assignRole('admin');

    return $user;
}

it('allows an authorized user to create a kick-off', function () {
    $user = createKickoffUser();
    $project = Project::factory()->create();

    $payload = [
        'purchase_order_number' => 'PO-1001',
        'scheduled_at' => now()->addDay()->format('Y-m-d H:i'),
        'meeting_mode' => 'online',
        'stakeholders' => 'QA Lead, PM',
        'requirements_summary' => 'Core modules and integrations',
        'timeline_summary' => 'Six-week delivery plan',
        'notes' => 'Confirm stakeholders before the call.',
        'status' => ProjectKickoff::STATUSES[0],
    ];

    $this->actingAs($user)
        ->post(route('projects.kickoffs.store', $project), $payload)
        ->assertRedirect(route('projects.kickoffs.show', $project));

    expect($project->refresh()->kickoff)->not->toBeNull();
});

it('prevents creating more than one kick-off per project', function () {
    $user = createKickoffUser();
    $project = Project::factory()->create();

    $project->kickoff()->create([
        'status' => ProjectKickoff::STATUSES[0],
    ]);

    $this->actingAs($user)
        ->post(route('projects.kickoffs.store', $project), [
            'status' => ProjectKickoff::STATUSES[0],
        ])
        ->assertRedirect(route('projects.kickoffs.edit', $project))
        ->assertSessionHas('error');
});

it('validates kick-off input', function () {
    $user = createKickoffUser();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->post(route('projects.kickoffs.store', $project), [
            'status' => 'invalid-status',
        ])
        ->assertSessionHasErrors(['status']);
});

it('forbids kick-off access for users without roles', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->get(route('projects.kickoffs.show', $project))
        ->assertForbidden();
});
