<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignStoryRequest;
use App\Http\Requests\MoveStoryRequest;
use App\Http\Requests\SendStoryToTestingRequest;
use App\Http\Requests\StoreStoryRequest;
use App\Http\Requests\UpdateStoryRequest;
use App\Models\Project;
use App\Models\ProjectBoard;
use App\Models\ProjectBoardColumn;
use App\Models\Story;
use App\Models\StoryStatusHistory;
use App\Models\TestingCard;
use App\Services\IssueKeyGenerator;
use App\Services\ProjectBoardProvisioner;
use Illuminate\Http\RedirectResponse;

class KanbanStoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:kanban.create')->only(['store']);
        $this->middleware('permission:kanban.edit')->only(['assign', 'move']);
    }

    public function store(
        StoreStoryRequest $request,
        Project $project,
        ProjectBoard $board,
        IssueKeyGenerator $issueKeyGenerator
    ): RedirectResponse {
        $todoColumn = $board->columns()
            ->where('name', ProjectBoard::DEVELOPMENT_COLUMNS[0])
            ->first();

        if (! $todoColumn instanceof ProjectBoardColumn) {
            return back()->with('error', 'The Todo column is missing for this board.');
        }

        $validated = $request->validated();
        $userId = $request->user()?->id;
        $labels = $this->splitList($validated['labels'] ?? null);
        $links = $this->splitLines($validated['reference_links'] ?? null);
        $estimateUnit = $validated['estimate_unit'] ?? null;

        if ($estimateUnit === null && array_key_exists('estimate', $validated) && $validated['estimate'] !== null) {
            $estimateUnit = 'points';
        }

        Story::query()->getConnection()->transaction(function () use (
            $board,
            $todoColumn,
            $validated,
            $userId,
            $project,
            $issueKeyGenerator,
            $labels,
            $links,
            $estimateUnit
        ) {
            $issue = $issueKeyGenerator->next($project);

            $story = $board->stories()->create([
                'project_board_column_id' => $todoColumn->id,
                'issue_key' => $issue['issue_key'],
                'issue_number' => $issue['issue_number'],
                'title' => $validated['title'],
                'priority' => $validated['priority'],
                'due_date' => $validated['due_date'],
                'description' => $validated['description'],
                'acceptance_criteria' => $validated['acceptance_criteria'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'assignee_id' => $validated['assignee_id'],
                'labels' => $labels ?: null,
                'estimate' => $validated['estimate'] ?? null,
                'estimate_unit' => $estimateUnit,
                'reference_links' => $links ?: null,
                'created_by' => $userId,
            ]);

            StoryStatusHistory::query()->create([
                'story_id' => $story->id,
                'from_column_id' => null,
                'to_column_id' => $todoColumn->id,
                'moved_by' => $userId,
                'moved_at' => now(),
                'reason' => 'Created',
            ]);
        });

        return redirect()
            ->route('projects.kanban.boards.show', [$project, $board])
            ->with('success', 'Story created.');
    }

    public function assign(AssignStoryRequest $request, Project $project, ProjectBoard $board, Story $story): RedirectResponse
    {
        $assigneeId = $request->integer('assignee_id');
        $assignedColumn = $board->columns()
            ->where('name', ProjectBoard::DEVELOPMENT_COLUMNS[1])
            ->first();
        $todoColumn = $board->columns()
            ->where('name', ProjectBoard::DEVELOPMENT_COLUMNS[0])
            ->first();

        Story::query()->getConnection()->transaction(function () use ($story, $assigneeId, $assignedColumn, $todoColumn, $request) {
            $fromColumnId = $story->project_board_column_id;

            $story->update([
                'assignee_id' => $assigneeId,
                'project_board_column_id' => $assignedColumn instanceof ProjectBoardColumn
                    && $todoColumn instanceof ProjectBoardColumn
                    && $story->project_board_column_id === $todoColumn->id
                    ? $assignedColumn->id
                    : $story->project_board_column_id,
            ]);

            if ($assignedColumn instanceof ProjectBoardColumn && $story->project_board_column_id !== $fromColumnId) {
                StoryStatusHistory::query()->create([
                    'story_id' => $story->id,
                    'from_column_id' => $fromColumnId,
                    'to_column_id' => $story->project_board_column_id,
                    'moved_by' => $request->user()?->id,
                    'moved_at' => now(),
                    'reason' => 'Assigned',
                ]);
            }
        });

        return redirect()
            ->route('projects.kanban.boards.show', [$project, $board])
            ->with('success', 'Story assigned.');
    }

    public function move(
        MoveStoryRequest $request,
        Project $project,
        ProjectBoard $board,
        Story $story,
    ): RedirectResponse {
        $columnId = $request->integer('column_id');
        $toColumn = $board->columns()->whereKey($columnId)->first();

        if (! $toColumn instanceof ProjectBoardColumn) {
            return back()->with('error', 'The selected column is invalid.');
        }

        $userId = $request->user()?->id;
        $toColumnName = $toColumn->name;

        if ($toColumnName === ProjectBoard::DEVELOPMENT_COLUMNS[1] && $story->assignee_id === null) {
            return back()->with('error', 'Assign a developer before moving to Assigned.');
        }

        if ($toColumnName === ProjectBoard::DEVELOPMENT_COLUMNS[2] && $story->assignee_id === null) {
            return back()->with('error', 'Start work by assigning a developer.');
        }

        if ($toColumnName === ProjectBoard::DEVELOPMENT_COLUMNS[3]) {
            if ($story->description === null || trim($story->description) === '') {
                return back()->with('error', 'Add a description before sending to review.');
            }

            if (! $story->database_changes_confirmed || ! $story->page_mappings_confirmed) {
                return back()->with('error', 'Confirm database changes and page mappings before review.');
            }
        }

        if ($toColumnName === ProjectBoard::DEVELOPMENT_COLUMNS[4] && ! $request->filled('blocker_reason')) {
            return back()->with('error', 'Select a blocker reason to move into Blocker.');
        }

        if ($toColumnName === ProjectBoard::DEVELOPMENT_COLUMNS[5]) {
            $story->loadMissing('testingCard');

            if ($story->testingCard === null || $story->testingCard->result !== TestingCard::RESULT_PASS) {
                return back()->with('error', 'Testing must pass before completing this story.');
            }
        }

        Story::query()->getConnection()->transaction(function () use ($story, $toColumn, $userId, $request, $toColumnName) {
            $fromColumnId = $story->project_board_column_id;

            $story->update([
                'project_board_column_id' => $toColumn->id,
                'blocker_reason' => $toColumnName === ProjectBoard::DEVELOPMENT_COLUMNS[4]
                    ? $request->string('blocker_reason')->trim()->toString()
                    : null,
            ]);

            StoryStatusHistory::query()->create([
                'story_id' => $story->id,
                'from_column_id' => $fromColumnId,
                'to_column_id' => $toColumn->id,
                'moved_by' => $userId,
                'moved_at' => now(),
                'reason' => $request->string('reason')->trim()->toString() ?: null,
                'notes' => $request->string('notes')->trim()->toString() ?: null,
            ]);
        });

        return redirect()
            ->route('projects.kanban.boards.show', [$project, $board])
            ->with('success', 'Story moved.');
    }

    public function update(
        UpdateStoryRequest $request,
        Project $project,
        ProjectBoard $board,
        Story $story
    ): RedirectResponse {
        $validated = $request->validated();

        if (array_key_exists('labels', $validated)) {
            $validated['labels'] = $this->splitList($validated['labels']);
        }

        if (array_key_exists('reference_links', $validated)) {
            $validated['reference_links'] = $this->splitLines($validated['reference_links']);
        }

        if (array_key_exists('estimate_unit', $validated) && $validated['estimate_unit'] === null) {
            $validated['estimate_unit'] = $validated['estimate'] !== null ? 'points' : null;
        }

        if ($request->boolean('database_changes_confirmed') && ! array_key_exists('database_changes', $validated)) {
            $validated['database_changes'] = [];
        }

        if ($request->boolean('page_mappings_confirmed') && ! array_key_exists('page_mappings', $validated)) {
            $validated['page_mappings'] = [];
        }

        $story->update($validated);

        return redirect()
            ->route('projects.kanban.boards.show', [$project, $board])
            ->with('success', 'Story updated.');
    }

    public function sendToTesting(
        SendStoryToTestingRequest $request,
        Project $project,
        ProjectBoard $board,
        Story $story,
        ProjectBoardProvisioner $provisioner
    ): RedirectResponse {
        $reviewColumn = $board->columns()
            ->where('name', ProjectBoard::DEVELOPMENT_COLUMNS[3])
            ->first();

        if (! $reviewColumn instanceof ProjectBoardColumn || $story->project_board_column_id !== $reviewColumn->id) {
            return back()->with('error', 'Move the story to Review before sending to testing.');
        }

        $project->loadMissing('testingBoard');

        if ($project->testingBoard === null) {
            $provisioner->ensureBoards($project);
            $project->load('testingBoard');
        }

        $testingBoard = $project->testingBoard;

        if (! $testingBoard) {
            return back()->with('error', 'Testing board is missing for this project.');
        }

        $testerId = $request->integer('tester_id');
        $targetColumnName = $testerId > 0 ? ProjectBoard::TESTING_COLUMNS[1] : ProjectBoard::TESTING_COLUMNS[0];
        $targetColumn = $testingBoard->columns()->where('name', $targetColumnName)->first();

        if (! $targetColumn instanceof ProjectBoardColumn) {
            return back()->with('error', 'Testing column is missing for this board.');
        }

        TestingCard::query()->updateOrCreate(
            ['story_id' => $story->id],
            [
                'project_board_id' => $testingBoard->id,
                'project_board_column_id' => $targetColumn->id,
                'tester_id' => $testerId > 0 ? $testerId : null,
                'created_by' => $request->user()?->id,
            ]
        );

        return redirect()
            ->route('projects.kanban.boards.show', [$project, $board])
            ->with('success', 'Story sent to testing.');
    }

    /**
     * @return list<string>
     */
    private function splitList(?string $value): array
    {
        if ($value === null) {
            return [];
        }

        $items = array_map('trim', explode(',', $value));

        return array_values(array_filter($items, fn (string $item) => $item !== ''));
    }

    /**
     * @return list<string>
     */
    private function splitLines(?string $value): array
    {
        if ($value === null) {
            return [];
        }

        $lines = preg_split("/\R/", $value) ?: [];
        $lines = array_map('trim', $lines);

        return array_values(array_filter($lines, fn (string $line) => $line !== ''));
    }
}
