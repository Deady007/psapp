<?php

use App\Mail\ProjectKickoffRescheduledMail;
use App\Mail\ProjectKickoffScheduledMail;
use App\Models\Contact;
use App\Models\Project;
use App\Models\ProjectKickoff;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(RbacSeeder::class);
    $this->withoutMiddleware(ValidateCsrfToken::class);
    Mail::fake();
});

function createKickoffUser(): User
{
    $user = User::factory()->create();
    $user->assignRole('admin');

    return $user;
}

it('allows planning a kick-off', function () {
    $user = createKickoffUser();
    $project = Project::factory()->create();
    $contact = Contact::query()->create([
        'customer_id' => $project->customer_id,
        'name' => 'QA Lead',
        'email' => 'qa@example.com',
        'phone' => '1234567890',
        'designation' => 'QA Lead',
    ]);

    $payload = [
        'purchase_order_number' => 'PO-1001',
        'stakeholders' => [
            'customer:'.$project->customer_id,
            'contact:'.$contact->id,
            'user:'.$user->id,
        ],
        'notes' => 'Confirm stakeholders before the call.',
    ];

    $this->actingAs($user)
        ->post(route('projects.kickoffs.plan.store', $project), $payload)
        ->assertRedirect(route('projects.kickoffs.schedule', $project));

    $kickoff = $project->refresh()->kickoff;

    expect($kickoff)->not->toBeNull()
        ->and($kickoff->status)->toBe('planned')
        ->and($kickoff->planned_at)->not->toBeNull();
});

it('prevents planning more than one kick-off per project', function () {
    $user = createKickoffUser();
    $project = Project::factory()->create();

    $project->kickoff()->create([
        'status' => 'planned',
        'planned_at' => now(),
    ]);

    $this->actingAs($user)
        ->post(route('projects.kickoffs.plan.store', $project), [])
        ->assertRedirect(route('projects.kickoffs.show', $project))
        ->assertSessionHas('error');
});

it('restores a deleted kick-off when planning again', function () {
    $user = createKickoffUser();
    $project = Project::factory()->create();
    $kickoff = $project->kickoff()->create([
        'status' => 'scheduled',
        'planned_at' => now()->subDay(),
        'scheduled_at' => now()->addDay(),
        'meeting_mode' => 'virtual_meet',
        'meeting_link' => 'https://meet.example.com/room',
    ]);

    $kickoff->delete();

    $this->actingAs($user)
        ->post(route('projects.kickoffs.plan.store', $project), [
            'purchase_order_number' => 'PO-RESTORE',
            'notes' => 'Replanned kickoff.',
        ])
        ->assertRedirect(route('projects.kickoffs.schedule', $project));

    $restored = ProjectKickoff::withTrashed()
        ->where('project_id', $project->id)
        ->first();

    expect($restored)->not->toBeNull()
        ->and($restored->trashed())->toBeFalse()
        ->and($restored->status)->toBe('planned')
        ->and($restored->purchase_order_number)->toBe('PO-RESTORE')
        ->and($restored->scheduled_at)->toBeNull()
        ->and($restored->completed_at)->toBeNull();
});

it('schedules a planned kick-off', function () {
    $user = createKickoffUser();
    $project = Project::factory()->create();

    $project->kickoff()->create([
        'status' => 'planned',
        'planned_at' => now(),
    ]);

    $payload = [
        'scheduled_at' => now()->addDay()->format('Y-m-d H:i'),
        'meeting_mode' => 'onsite',
        'site_location' => 'HQ Boardroom',
    ];

    $this->actingAs($user)
        ->put(route('projects.kickoffs.schedule.update', $project), $payload)
        ->assertRedirect(route('projects.kickoffs.complete', $project));

    $kickoff = $project->refresh()->kickoff;

    expect($kickoff->status)->toBe('scheduled')
        ->and($kickoff->scheduled_at)->not->toBeNull();

    Mail::assertSent(ProjectKickoffScheduledMail::class, function (ProjectKickoffScheduledMail $mail) use ($project, $kickoff): bool {
        return $mail->project->is($project) && $mail->kickoff->is($kickoff);
    });
});

it('reschedules a scheduled kick-off and emails the update', function () {
    $user = createKickoffUser();
    $project = Project::factory()->create();
    $originalSchedule = now()->addDay();

    $project->kickoff()->create([
        'status' => 'scheduled',
        'planned_at' => now()->subDay(),
        'scheduled_at' => $originalSchedule,
        'meeting_mode' => 'virtual_meet',
        'meeting_link' => 'https://meet.example.com/room',
    ]);

    $payload = [
        'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i'),
        'meeting_mode' => 'virtual_teams',
        'meeting_link' => 'https://teams.example.com/room',
    ];

    $this->actingAs($user)
        ->put(route('projects.kickoffs.schedule.update', $project), $payload)
        ->assertRedirect(route('projects.kickoffs.complete', $project));

    $kickoff = $project->refresh()->kickoff;

    expect($kickoff->status)->toBe('scheduled')
        ->and($kickoff->meeting_mode)->toBe('virtual_teams');

    Mail::assertSent(ProjectKickoffRescheduledMail::class, function (ProjectKickoffRescheduledMail $mail) use ($project, $kickoff, $originalSchedule): bool {
        return $mail->project->is($project)
            && $mail->kickoff->is($kickoff)
            && $mail->previousScheduledAt?->eq($originalSchedule);
    });
});

it('completes a scheduled kick-off with requirement summary', function () {
    $user = createKickoffUser();
    $project = Project::factory()->create();

    $project->kickoff()->create([
        'status' => 'scheduled',
        'planned_at' => now()->subDay(),
        'scheduled_at' => now()->addDay(),
        'meeting_mode' => 'virtual_meet',
        'meeting_link' => 'https://meet.example.com/room',
    ]);

    $payload = [
        'requirements_summary' => 'Core modules and integrations',
        'timeline_summary' => 'Six-week delivery plan',
        'notes' => 'Confirm follow-ups.',
    ];

    $this->actingAs($user)
        ->put(route('projects.kickoffs.complete.update', $project), $payload)
        ->assertRedirect(route('projects.kickoffs.show', $project));

    $kickoff = $project->refresh()->kickoff;

    expect($kickoff->status)->toBe('completed')
        ->and($kickoff->requirements_summary)->toBe('Core modules and integrations')
        ->and($kickoff->completed_at)->not->toBeNull();
});

it('validates completion requires a requirements summary', function () {
    $user = createKickoffUser();
    $project = Project::factory()->create();

    $project->kickoff()->create([
        'status' => 'scheduled',
        'planned_at' => now()->subDay(),
        'scheduled_at' => now()->addDay(),
        'meeting_mode' => 'virtual_teams',
        'meeting_link' => 'https://teams.example.com/room',
    ]);

    $this->actingAs($user)
        ->put(route('projects.kickoffs.complete.update', $project), [
            'requirements_summary' => '',
        ])
        ->assertSessionHasErrors(['requirements_summary']);
});

it('forbids kick-off access for users without roles', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->get(route('projects.kickoffs.show', $project))
        ->assertForbidden();
});
