<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Jobs\CreateProjectDriveFolders;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:projects.view')->only(['index', 'show']);
        $this->middleware('permission:projects.create')->only(['create', 'store']);
        $this->middleware('permission:projects.edit')->only(['edit', 'update']);
        $this->middleware('permission:projects.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $query = Project::query()
            ->with('customer')
            ->latest();

        if (($customerId = $request->integer('customer_id')) > 0) {
            $query->where('customer_id', $customerId);
        }

        if ($request->filled('status') && in_array($request->string('status')->toString(), Project::STATUSES, true)) {
            $query->where('status', $request->string('status')->toString());
        }

        $projects = $query
            ->paginate(10)
            ->withQueryString();

        return view('projects.index', [
            'projects' => $projects,
            'customers' => $customers,
            'statuses' => Project::STATUSES,
            'filters' => [
                'customer_id' => $request->integer('customer_id') ?: null,
                'status' => $request->string('status')->toString() ?: null,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('projects.create', [
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => Project::STATUSES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): RedirectResponse|JsonResponse
    {
        $project = Project::create($request->safe()->except('products'));
        $project->products()->sync($request->input('products', []));
        CreateProjectDriveFolders::dispatch($project->id, $request->user()?->id);

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.show', $project),
                'message' => 'Project created.',
            ]);
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): View
    {
        $project->load(['customer', 'products', 'kickoff'])
            ->loadCount(['requirements', 'products']);

        return view('projects.show', [
            'project' => $project,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): View
    {
        $project->load('products');

        return view('projects.edit', [
            'project' => $project,
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => Project::STATUSES,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse|JsonResponse
    {
        $project->update($request->safe()->except('products'));
        $project->products()->sync($request->input('products', []));

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.show', $project),
                'message' => 'Project updated.',
            ]);
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): RedirectResponse|JsonResponse
    {
        $project->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'redirect' => route('projects.index'),
                'message' => 'Project deleted.',
            ]);
        }

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted.');
    }
}
