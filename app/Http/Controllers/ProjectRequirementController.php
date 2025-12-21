<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequirementRequest;
use App\Http\Requests\UpdateProjectRequirementRequest;
use App\Models\Project;
use App\Models\ProjectRequirement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectRequirementController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:project_requirements.view')->only(['index']);
        $this->middleware('permission:project_requirements.create')->only(['create', 'store']);
        $this->middleware('permission:project_requirements.edit')->only(['edit', 'update']);
        $this->middleware('permission:project_requirements.delete')->only(['destroy']);
    }

    public function index(Project $project): View
    {
        $requirements = $project->requirements()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('projects.requirements.index', [
            'project' => $project,
            'requirements' => $requirements,
            'priorities' => ProjectRequirement::PRIORITIES,
            'statuses' => ProjectRequirement::STATUSES,
        ]);
    }

    public function create(Project $project): View
    {
        return view('projects.requirements.create', [
            'project' => $project,
            'priorities' => ProjectRequirement::PRIORITIES,
            'statuses' => ProjectRequirement::STATUSES,
        ]);
    }

    public function store(StoreProjectRequirementRequest $request, Project $project): RedirectResponse|JsonResponse
    {
        $project->requirements()->create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.requirements.index', $project),
                'message' => 'Requirement created.',
            ]);
        }

        return redirect()
            ->route('projects.requirements.index', $project)
            ->with('success', 'Requirement created.');
    }

    public function edit(Project $project, ProjectRequirement $requirement): View
    {
        return view('projects.requirements.edit', [
            'project' => $project,
            'requirement' => $requirement,
            'priorities' => ProjectRequirement::PRIORITIES,
            'statuses' => ProjectRequirement::STATUSES,
        ]);
    }

    public function update(UpdateProjectRequirementRequest $request, Project $project, ProjectRequirement $requirement): RedirectResponse|JsonResponse
    {
        $requirement->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.requirements.index', $project),
                'message' => 'Requirement updated.',
            ]);
        }

        return redirect()
            ->route('projects.requirements.index', $project)
            ->with('success', 'Requirement updated.');
    }

    public function destroy(Project $project, ProjectRequirement $requirement): RedirectResponse|JsonResponse
    {
        $requirement->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.requirements.index', $project),
                'message' => 'Requirement deleted.',
            ]);
        }

        return redirect()
            ->route('projects.requirements.index', $project)
            ->with('success', 'Requirement deleted.');
    }
}
