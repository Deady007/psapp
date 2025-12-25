<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBugRequest;
use App\Http\Requests\UpdateBugRequest;
use App\Models\Bug;
use App\Models\Project;
use App\Models\ProjectBoard;
use App\Models\ProjectBoardColumn;
use App\Models\Story;
use App\Models\StoryStatusHistory;
use App\Models\TestingCard;
use App\Services\IssueKeyGenerator;
use Illuminate\Http\RedirectResponse;

class KanbanBugController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:kanban.create')->only(['store']);
        $this->middleware('permission:kanban.edit')->only(['update']);
    }

    public function store(
        StoreBugRequest $request,
        Project $project,
        ProjectBoard $board,
        IssueKeyGenerator $issueKeyGenerator
    ): RedirectResponse {
        $validated = $request->validated();
        $testingCard = TestingCard::query()->findOrFail($validated['testing_card_id']);
        $userId = $request->user()?->id;

        Bug::query()->getConnection()->transaction(function () use ($board, $testingCard, $validated, $userId, $issueKeyGenerator, $project) {
            $issue = $issueKeyGenerator->next($project);

            $board->bugs()->create([
                'story_id' => $testingCard->story_id,
                'testing_card_id' => $testingCard->id,
                'issue_key' => $issue['issue_key'],
                'issue_number' => $issue['issue_number'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'severity' => $validated['severity'],
                'steps_to_reproduce' => $validated['steps_to_reproduce'] ?? null,
                'status' => Bug::STATUS_OPEN,
                'assignee_id' => $validated['assignee_id'] ?? null,
                'reported_by' => $userId,
                'found_at' => now(),
            ]);

            $story = $testingCard->story;

            if (! $story) {
                return;
            }

            $developmentBoard = $story->board;

            if (! $developmentBoard) {
                return;
            }

            $blockerColumn = $developmentBoard->columns()
                ->where('name', ProjectBoard::DEVELOPMENT_COLUMNS[4])
                ->first();

            if (! $blockerColumn instanceof ProjectBoardColumn) {
                return;
            }

            $fromColumnId = $story->project_board_column_id;

            $story->update([
                'project_board_column_id' => $blockerColumn->id,
                'blocker_reason' => Story::BLOCKER_REASON_BUG_FOUND,
            ]);

            StoryStatusHistory::query()->create([
                'story_id' => $story->id,
                'from_column_id' => $fromColumnId,
                'to_column_id' => $blockerColumn->id,
                'moved_by' => $userId,
                'moved_at' => now(),
                'reason' => 'Bug created',
            ]);
        });

        return redirect()
            ->route('projects.kanban.boards.show', [$project, $board])
            ->with('success', 'Bug created.');
    }

    public function update(UpdateBugRequest $request, Project $project, ProjectBoard $board, Bug $bug): RedirectResponse
    {
        $bug->update($request->validated());

        return redirect()
            ->route('projects.kanban.boards.show', [$project, $board])
            ->with('success', 'Bug updated.');
    }
}
