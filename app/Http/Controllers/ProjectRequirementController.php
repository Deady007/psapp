<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequirementImportRequest;
use App\Http\Requests\StoreProjectRequirementRequest;
use App\Http\Requests\StoreProjectRequirementTranscriptRequest;
use App\Http\Requests\UpdateProjectRequirementRequest;
use App\Models\Project;
use App\Models\ProjectRequirement;
use App\Services\GeminiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class ProjectRequirementController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:project_requirements.view')->only(['index']);
        $this->middleware('permission:project_requirements.create')->only([
            'create',
            'store',
            'import',
            'previewImport',
            'storeImport',
        ]);
        $this->middleware('permission:project_requirements.edit')->only(['edit', 'update']);
        $this->middleware('permission:project_requirements.delete')->only(['destroy']);
    }

    public function index(Project $project): View
    {
        $modules = $project->requirements()
            ->select('module_name')
            ->selectRaw('count(*) as total')
            ->groupBy('module_name')
            ->orderBy('module_name')
            ->get();

        $requestedModule = request()->string('module')->trim()->toString();
        $selectedModule = $requestedModule !== '' ? $requestedModule : null;

        if ($selectedModule !== null && $modules->where('module_name', $selectedModule)->isEmpty()) {
            $selectedModule = null;
        }

        if ($selectedModule === null && $modules->isNotEmpty()) {
            $selectedModule = $modules->first()->module_name;
        }

        $requirementsQuery = $project->requirements()->latest();

        if ($selectedModule !== null) {
            $requirementsQuery->where('module_name', $selectedModule);
        }

        $requirements = $requirementsQuery
            ->paginate(10)
            ->withQueryString();

        $viewData = [
            'project' => $project,
            'requirements' => $requirements,
            'selectedModule' => $selectedModule,
        ];

        if (request()->ajax()) {
            return view('projects.requirements.partials.list', $viewData);
        }

        return view('projects.requirements.index', [
            ...$viewData,
            'modules' => $modules,
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

    public function import(Project $project): View
    {
        $context = $this->importContext($project);

        return view('projects.requirements.import', [
            'project' => $project,
            'priorities' => ProjectRequirement::PRIORITIES,
            'statuses' => ProjectRequirement::STATUSES,
            'analysisMode' => 'fast',
            ...$context,
        ]);
    }

    public function previewImport(
        StoreProjectRequirementTranscriptRequest $request,
        Project $project,
        GeminiClient $client
    ): View|RedirectResponse {
        $context = $this->importContext($project, $request->string('source')->trim()->toString());
        $analysisMode = $request->string('analysis_mode')->lower()->toString() === 'deep' ? 'deep' : 'fast';
        $timeLimit = $analysisMode === 'deep' ? 300 : 180;

        if (function_exists('set_time_limit')) {
            set_time_limit($timeLimit);
        }

        try {
            $transcript = $request->file('transcript')->get();
            $drafts = $client->extractRequirementsFromTranscript(
                $transcript,
                $this->transcriptContext($project),
                $analysisMode
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Unable to analyze the transcript right now. Please try again.');
        }

        if ($drafts === []) {
            return back()
                ->withInput()
                ->with('error', 'No requirements were detected in the transcript.');
        }

        if ($context['source'] === 'kickoff') {
            $this->storeKickoffTranscript($project, $request->file('transcript'));
        }

        return view('projects.requirements.import', [
            'project' => $project,
            'priorities' => ProjectRequirement::PRIORITIES,
            'statuses' => ProjectRequirement::STATUSES,
            'drafts' => $drafts,
            'transcriptName' => $request->file('transcript')->getClientOriginalName(),
            'analysisMode' => $analysisMode,
            ...$context,
        ]);
    }

    public function store(StoreProjectRequirementRequest $request, Project $project): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        $message = 'Requirement created.';

        if (array_key_exists('requirements', $validated)) {
            $project->requirements()->createMany($validated['requirements']);
            $createdCount = count($validated['requirements']);
            $message = $createdCount === 1
                ? 'Requirement created.'
                : sprintf('%d requirements created.', $createdCount);
        } else {
            $project->requirements()->create($validated);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.requirements.index', $project),
                'message' => $message,
            ]);
        }

        return redirect()
            ->route('projects.requirements.index', $project)
            ->with('success', $message);
    }

    public function storeImport(StoreProjectRequirementImportRequest $request, Project $project): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        $requirements = $validated['requirements'];
        $project->requirements()->createMany($requirements);

        $createdCount = count($requirements);
        $message = $createdCount === 1
            ? 'Requirement imported.'
            : sprintf('%d requirements imported.', $createdCount);

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.requirements.index', $project),
                'message' => $message,
            ]);
        }

        return redirect()
            ->route('projects.requirements.index', $project)
            ->with('success', $message);
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

    /**
     * @return array{source: string|null, backUrl: string, backLabel: string}
     */
    private function importContext(Project $project, ?string $source = null): array
    {
        $source = $source !== null ? trim($source) : request()->string('source')->trim()->toString();

        if ($source === 'kickoff') {
            return [
                'source' => 'kickoff',
                'backUrl' => route('projects.kickoffs.show', $project),
                'backLabel' => __('Back to Kick-off'),
            ];
        }

        return [
            'source' => null,
            'backUrl' => route('projects.requirements.index', $project),
            'backLabel' => __('Back to Requirements'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function transcriptContext(Project $project): array
    {
        $project->loadMissing('products');

        $context = [
            'Project' => $project->name,
        ];

        if ($project->products->isNotEmpty()) {
            $context['Products'] = $project->products->pluck('name')->implode(', ');
        }

        return $context;
    }

    private function storeKickoffTranscript(Project $project, UploadedFile $file): void
    {
        $kickoff = $project->kickoff;

        if ($kickoff === null) {
            return;
        }

        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = Str::slug($baseName);
        $safeName = $slug !== '' ? $slug : 'transcript';
        $extension = $file->getClientOriginalExtension() ?: 'txt';
        $filename = sprintf('%s-%s-%s.%s', $project->id, now()->format('YmdHis'), $safeName, $extension);

        $path = $file->storeAs(
            sprintf('kickoff-transcripts/%s', $project->id),
            $filename,
            'local'
        );

        $kickoff->update([
            'transcript_path' => $path,
            'transcript_uploaded_at' => now(),
        ]);
    }
}
