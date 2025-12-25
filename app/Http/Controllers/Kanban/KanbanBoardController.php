<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectBoard;
use App\Models\User;
use App\Services\ProjectBoardProvisioner;
use Illuminate\View\View;

class KanbanBoardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:kanban.view');
    }

    public function index(Project $project, ProjectBoardProvisioner $provisioner): View
    {
        $provisioner->ensureBoards($project);

        $boards = $project->boards()
            ->orderBy('type')
            ->get();

        return view('kanban.index', [
            'project' => $project,
            'boards' => $boards,
        ]);
    }

    public function show(Project $project, ProjectBoard $board): View
    {
        $project->loadMissing(['developmentBoard', 'testingBoard']);

        $columns = $board->columns()
            ->orderBy('position')
            ->get();

        $viewMode = request()->string('view')->lower()->toString();
        $viewMode = in_array($viewMode, ['list', 'board'], true) ? $viewMode : 'board';

        $filters = [
            'search' => request()->string('search')->trim()->toString(),
            'assignee' => request()->string('assignee')->trim()->toString(),
            'priority' => request()->string('priority')->trim()->toString(),
            'status' => request()->string('status')->trim()->toString(),
            'due' => request()->string('due')->trim()->toString(),
            'has_bugs' => request()->boolean('has_bugs'),
            'overdue' => request()->boolean('overdue'),
            'my_items' => request()->boolean('my_items'),
            'ready' => request()->string('ready')->trim()->toString(),
        ];

        $documents = $board->documents()
            ->latest('generated_at')
            ->get();

        $developers = User::role('developer')
            ->orderBy('name')
            ->get(['id', 'name']);

        $testers = User::role('tester')
            ->orderBy('name')
            ->get(['id', 'name']);

        $viewData = [
            'project' => $project,
            'board' => $board,
            'columns' => $columns,
            'documents' => $documents,
            'developers' => $developers,
            'testers' => $testers,
            'filters' => $filters,
            'viewMode' => $viewMode,
        ];

        if ($board->isDevelopment()) {
            $storiesQuery = $board->stories()
                ->with(['assignee', 'tasks', 'testingCard.column', 'testingCard.tester', 'bugs', 'latestStatusHistory'])
                ->orderBy('created_at');

            if ($filters['search'] !== '') {
                $search = $filters['search'];
                $storiesQuery->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('issue_key', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($filters['assignee'] !== '') {
                $storiesQuery->where('assignee_id', (int) $filters['assignee']);
            }

            if ($filters['priority'] !== '') {
                $storiesQuery->where('priority', $filters['priority']);
            }

            if ($filters['status'] !== '') {
                $status = $filters['status'];
                $storiesQuery->whereHas('column', fn ($query) => $query->where('name', $status));
            }

            if ($filters['due'] === 'today') {
                $storiesQuery->whereDate('due_date', now()->toDateString());
            } elseif ($filters['due'] === 'week') {
                $storiesQuery->whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()]);
            }

            if ($filters['has_bugs']) {
                $storiesQuery->whereHas('bugs');
            }

            if ($filters['overdue']) {
                $storiesQuery->whereDate('due_date', '<', now()->toDateString());
            }

            if ($filters['my_items'] && auth()->check()) {
                $storiesQuery->where('assignee_id', auth()->id());
            }

            if ($filters['ready'] === 'review') {
                $storiesQuery->whereHas('column', fn ($query) => $query->where('name', ProjectBoard::DEVELOPMENT_COLUMNS[3]));
            }

            $stories = $storiesQuery->get();

            return view('kanban.show', [
                ...$viewData,
                'stories' => $stories,
                'storiesByColumn' => $stories->groupBy('project_board_column_id'),
                'testingCards' => collect(),
                'testingCardsByColumn' => collect(),
                'bugs' => collect(),
            ]);
        }

        $testingCardsQuery = $board->testingCards()
            ->with(['story', 'tester', 'bugs', 'column', 'story.assignee', 'story.latestStatusHistory'])
            ->orderBy('created_at');

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $testingCardsQuery->whereHas('story', function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('issue_key', 'like', "%{$search}%");
            });
        }

        if ($filters['assignee'] !== '') {
            $testingCardsQuery->where('tester_id', (int) $filters['assignee']);
        }

        if ($filters['priority'] !== '') {
            $priority = $filters['priority'];
            $testingCardsQuery->whereHas('story', fn ($query) => $query->where('priority', $priority));
        }

        if ($filters['status'] !== '') {
            $status = $filters['status'];
            $testingCardsQuery->whereHas('column', fn ($query) => $query->where('name', $status));
        }

        if ($filters['due'] === 'today') {
            $testingCardsQuery->whereHas('story', function ($query) {
                $query->whereDate('due_date', now()->toDateString());
            });
        } elseif ($filters['due'] === 'week') {
            $testingCardsQuery->whereHas('story', function ($query) {
                $query->whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()]);
            });
        }

        if ($filters['has_bugs']) {
            $testingCardsQuery->whereHas('bugs');
        }

        if ($filters['overdue']) {
            $testingCardsQuery->whereHas('story', function ($query) {
                $query->whereDate('due_date', '<', now()->toDateString());
            });
        }

        if ($filters['my_items'] && auth()->check()) {
            $testingCardsQuery->where('tester_id', auth()->id());
        }

        if ($filters['ready'] === 'testing') {
            $testingCardsQuery->whereHas('column', fn ($query) => $query->where('name', ProjectBoard::TESTING_COLUMNS[0]));
        }

        $testingCards = $testingCardsQuery->get();

        $bugs = $board->bugs()
            ->with(['story', 'assignee'])
            ->orderBy('created_at')
            ->get();

        return view('kanban.show', [
            ...$viewData,
            'stories' => collect(),
            'storiesByColumn' => collect(),
            'testingCards' => $testingCards,
            'testingCardsByColumn' => $testingCards->groupBy('project_board_column_id'),
            'bugs' => $bugs,
        ]);
    }
}
