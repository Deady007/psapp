<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectKickoffRequest;
use App\Http\Requests\UpdateProjectKickoffRequest;
use App\Models\Project;
use App\Models\ProjectKickoff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectKickoffController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:project_kickoffs.view')->only(['show']);
        $this->middleware('permission:project_kickoffs.create')->only(['create', 'store']);
        $this->middleware('permission:project_kickoffs.edit')->only(['edit', 'update']);
        $this->middleware('permission:project_kickoffs.delete')->only(['destroy']);
    }

    public function show(Project $project): View
    {
        $project->load(['kickoff', 'products']);

        return view('projects.kickoffs.show', [
            'project' => $project,
        ]);
    }

    public function create(Project $project): View|RedirectResponse
    {
        if ($project->kickoff !== null) {
            return redirect()
                ->route('projects.kickoffs.edit', $project)
                ->with('error', 'Kick-off already exists.');
        }

        $project->load('products');

        return view('projects.kickoffs.create', [
            'project' => $project,
            'statuses' => ProjectKickoff::STATUSES,
        ]);
    }

    public function store(StoreProjectKickoffRequest $request, Project $project): RedirectResponse|JsonResponse
    {
        if ($project->kickoff !== null) {
            return redirect()
                ->route('projects.kickoffs.edit', $project)
                ->with('error', 'Kick-off already exists.');
        }

        $kickoff = $project->kickoff()->create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.kickoffs.show', $project),
                'message' => 'Kick-off created.',
            ]);
        }

        return redirect()
            ->route('projects.kickoffs.show', $project)
            ->with('success', 'Kick-off created.');
    }

    public function edit(Project $project): View|RedirectResponse
    {
        if ($project->kickoff === null) {
            return redirect()
                ->route('projects.kickoffs.create', $project)
                ->with('error', 'Kick-off not found.');
        }

        $project->load(['kickoff', 'products']);

        return view('projects.kickoffs.edit', [
            'project' => $project,
            'kickoff' => $project->kickoff,
            'statuses' => ProjectKickoff::STATUSES,
        ]);
    }

    public function update(UpdateProjectKickoffRequest $request, Project $project): RedirectResponse|JsonResponse
    {
        $kickoff = $project->kickoff;

        if ($kickoff === null) {
            return redirect()
                ->route('projects.kickoffs.create', $project)
                ->with('error', 'Kick-off not found.');
        }

        $kickoff->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.kickoffs.show', $project),
                'message' => 'Kick-off updated.',
            ]);
        }

        return redirect()
            ->route('projects.kickoffs.show', $project)
            ->with('success', 'Kick-off updated.');
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
}
