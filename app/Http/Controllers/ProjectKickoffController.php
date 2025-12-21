<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteProjectKickoffRequest;
use App\Http\Requests\StoreProjectKickoffRequest;
use App\Http\Requests\UpdateProjectKickoffRequest;
use App\Mail\ProjectKickoffRescheduledMail;
use App\Mail\ProjectKickoffScheduledMail;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectKickoff;
use App\Models\ProjectKickoffStakeholder;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ProjectKickoffController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:project_kickoffs.view')->only(['show']);
        $this->middleware('permission:project_kickoffs.create')->only(['plan', 'storePlan']);
        $this->middleware('permission:project_kickoffs.edit')->only(['schedule', 'updateSchedule', 'complete', 'updateComplete']);
        $this->middleware('permission:project_kickoffs.delete')->only(['destroy']);
    }

    public function show(Project $project): View
    {
        $project->load(['kickoff.stakeholderLinks.stakeholder', 'products']);

        return view('projects.kickoffs.show', [
            'project' => $project,
        ]);
    }

    public function plan(Project $project): View|RedirectResponse
    {
        if ($project->kickoff !== null) {
            return redirect()
                ->route('projects.kickoffs.show', $project)
                ->with('error', 'Kick-off already planned.');
        }

        $project->load('products');

        return view('projects.kickoffs.plan', [
            'project' => $project,
            'stakeholderOptions' => $this->stakeholderOptions($project),
            'selectedStakeholders' => [],
        ]);
    }

    public function storePlan(StoreProjectKickoffRequest $request, Project $project): RedirectResponse|JsonResponse
    {
        $existing = ProjectKickoff::withTrashed()
            ->where('project_id', $project->id)
            ->first();

        if ($existing !== null && ! $existing->trashed()) {
            return redirect()
                ->route('projects.kickoffs.show', $project)
                ->with('error', 'Kick-off already planned.');
        }

        $payload = array_merge($request->validated(), [
            'status' => 'planned',
            'planned_at' => now(),
            'scheduled_at' => null,
            'completed_at' => null,
            'meeting_mode' => null,
            'site_location' => null,
            'meeting_link' => null,
            'requirements_summary' => null,
            'timeline_summary' => null,
        ]);

        if ($existing !== null && $existing->trashed()) {
            $existing->restore();
            $existing->fill($payload);
            $existing->save();
            $kickoff = $existing;
        } else {
            $kickoff = $project->kickoff()->create($payload);
        }

        $this->syncStakeholders($kickoff, $request->input('stakeholders', []));

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.kickoffs.schedule', $project),
                'message' => 'Kick-off planned.',
            ]);
        }

        return redirect()
            ->route('projects.kickoffs.schedule', $project)
            ->with('success', 'Kick-off planned.');
    }

    public function schedule(Project $project): View|RedirectResponse
    {
        $kickoff = $project->kickoff;

        if ($kickoff === null) {
            return redirect()
                ->route('projects.kickoffs.plan', $project)
                ->with('error', 'Plan the kick-off first.');
        }

        if ($kickoff->status === 'completed') {
            return redirect()
                ->route('projects.kickoffs.show', $project)
                ->with('error', 'Kick-off already completed.');
        }

        $project->load(['kickoff.stakeholderLinks.stakeholder', 'products']);

        return view('projects.kickoffs.schedule', [
            'project' => $project,
            'kickoff' => $kickoff,
            'stakeholderOptions' => $this->stakeholderOptions($project),
            'selectedStakeholders' => $this->selectedStakeholders($kickoff),
        ]);
    }

    public function updateSchedule(UpdateProjectKickoffRequest $request, Project $project): RedirectResponse|JsonResponse
    {
        $kickoff = $project->kickoff;

        if ($kickoff === null) {
            return redirect()
                ->route('projects.kickoffs.plan', $project)
                ->with('error', 'Plan the kick-off first.');
        }

        if ($kickoff->status === 'completed') {
            return redirect()
                ->route('projects.kickoffs.show', $project)
                ->with('error', 'Kick-off already completed.');
        }

        $wasScheduled = $kickoff->status === 'scheduled';
        $previousScheduledAt = $kickoff->scheduled_at?->copy();

        $kickoff->fill([
            'scheduled_at' => $request->date('scheduled_at')?->toDateTimeString(),
            'meeting_mode' => $request->string('meeting_mode')->toString(),
            'site_location' => $request->string('site_location')->toString() ?: null,
            'meeting_link' => $request->string('meeting_link')->toString() ?: null,
            'status' => 'scheduled',
        ]);
        $kickoff->save();

        $this->syncStakeholders($kickoff, $request->input('stakeholders', []));

        $scheduleChanged = $kickoff->wasChanged(['scheduled_at', 'meeting_mode', 'site_location', 'meeting_link']);

        if ($wasScheduled && $scheduleChanged) {
            $this->sendRescheduleEmail($project, $kickoff, $previousScheduledAt);
        } elseif (! $wasScheduled) {
            $this->sendScheduleEmail($project, $kickoff);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.kickoffs.complete', $project),
                'message' => 'Kick-off scheduled.',
            ]);
        }

        return redirect()
            ->route('projects.kickoffs.complete', $project)
            ->with('success', 'Kick-off scheduled.');
    }

    private function sendScheduleEmail(Project $project, ProjectKickoff $kickoff): void
    {
        $project->loadMissing(['products', 'kickoff.stakeholderLinks.stakeholder']);

        Mail::to('viral@fiscalox.com')->send(new ProjectKickoffScheduledMail($project, $kickoff));
    }

    private function sendRescheduleEmail(Project $project, ProjectKickoff $kickoff, ?\Carbon\Carbon $previousScheduledAt): void
    {
        $project->loadMissing(['products', 'kickoff.stakeholderLinks.stakeholder']);

        Mail::to('viral@fiscalox.com')->send(
            new ProjectKickoffRescheduledMail($project, $kickoff, $previousScheduledAt)
        );
    }

    public function complete(Project $project): View|RedirectResponse
    {
        $kickoff = $project->kickoff;

        if ($kickoff === null) {
            return redirect()
                ->route('projects.kickoffs.plan', $project)
                ->with('error', 'Plan the kick-off first.');
        }

        if ($kickoff->status === 'planned') {
            return redirect()
                ->route('projects.kickoffs.schedule', $project)
                ->with('error', 'Schedule the kick-off before completing it.');
        }

        $project->load(['kickoff.stakeholderLinks.stakeholder', 'products']);

        return view('projects.kickoffs.complete', [
            'project' => $project,
            'kickoff' => $kickoff,
            'stakeholderOptions' => $this->stakeholderOptions($project),
            'selectedStakeholders' => $this->selectedStakeholders($kickoff),
        ]);
    }

    public function updateComplete(CompleteProjectKickoffRequest $request, Project $project): RedirectResponse|JsonResponse
    {
        $kickoff = $project->kickoff;

        if ($kickoff === null) {
            return redirect()
                ->route('projects.kickoffs.plan', $project)
                ->with('error', 'Plan the kick-off first.');
        }

        if ($kickoff->status === 'planned') {
            return redirect()
                ->route('projects.kickoffs.schedule', $project)
                ->with('error', 'Schedule the kick-off before completing it.');
        }

        $kickoff->update([
            'requirements_summary' => $request->string('requirements_summary')->toString(),
            'timeline_summary' => $request->string('timeline_summary')->toString() ?: null,
            'notes' => $request->string('notes')->toString() ?: null,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->syncStakeholders($kickoff, $request->input('stakeholders', []));

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.kickoffs.show', $project),
                'message' => 'Kick-off completed.',
            ]);
        }

        return redirect()
            ->route('projects.kickoffs.show', $project)
            ->with('success', 'Kick-off completed.');
    }

    public function destroy(Project $project): RedirectResponse|JsonResponse
    {
        $kickoff = $project->kickoff;

        if ($kickoff === null) {
            return redirect()
                ->route('projects.kickoffs.show', $project)
                ->with('error', 'Kick-off not found.');
        }

        $kickoff->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.kickoffs.show', $project),
                'message' => 'Kick-off deleted.',
            ]);
        }

        return redirect()
            ->route('projects.kickoffs.show', $project)
            ->with('success', 'Kick-off deleted.');
    }

    /**
     * @return array<string, \Illuminate\Support\Collection<int, object>>
     */
    private function stakeholderOptions(Project $project): array
    {
        return [
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
            'contacts' => Contact::query()->orderBy('name')->get(['id', 'name']),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
        ];
    }

    /**
     * @return list<string>
     */
    private function selectedStakeholders(?ProjectKickoff $kickoff): array
    {
        if ($kickoff === null) {
            return [];
        }

        $map = array_flip($this->stakeholderTypeMap());

        return $kickoff->stakeholderLinks
            ->map(function (ProjectKickoffStakeholder $link) use ($map): string {
                $type = $map[$link->stakeholder_type] ?? $link->stakeholder_type;

                return $type.':'.$link->stakeholder_id;
            })
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $tokens
     */
    private function syncStakeholders(ProjectKickoff $kickoff, array $tokens): void
    {
        $map = $this->stakeholderTypeMap();

        $records = collect($tokens)
            ->filter(fn ($token) => is_string($token) && str_contains($token, ':'))
            ->map(function (string $token) use ($map): ?array {
                [$type, $id] = explode(':', $token, 2);

                if (! array_key_exists($type, $map) || ! ctype_digit($id)) {
                    return null;
                }

                return [
                    'stakeholder_type' => $map[$type],
                    'stakeholder_id' => (int) $id,
                ];
            })
            ->filter()
            ->unique(fn (array $record) => $record['stakeholder_type'].'-'.$record['stakeholder_id'])
            ->values();

        $kickoff->stakeholderLinks()->delete();

        if ($records->isNotEmpty()) {
            $kickoff->stakeholderLinks()->createMany($records->all());
        }
    }

    /**
     * @return array<string, class-string>
     */
    private function stakeholderTypeMap(): array
    {
        return [
            'customer' => Customer::class,
            'contact' => Contact::class,
            'user' => User::class,
        ];
    }
}
