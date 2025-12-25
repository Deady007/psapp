<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignTestingCardRequest;
use App\Http\Requests\MoveTestingCardRequest;
use App\Http\Requests\RecordTestingResultRequest;
use App\Models\Bug;
use App\Models\Project;
use App\Models\ProjectBoard;
use App\Models\ProjectBoardColumn;
use App\Models\Story;
use App\Models\StoryStatusHistory;
use App\Models\TestingCard;
use App\Services\IssueKeyGenerator;
use Illuminate\Http\RedirectResponse;

class KanbanTestingCardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:kanban.edit');
    }

    public function assign(
        AssignTestingCardRequest $request,
        Project $project,
        ProjectBoard $board,
        TestingCard $testingCard
    ): RedirectResponse {
        $testerId = $request->integer('tester_id');
        $assignedColumn = $board->columns()
            ->where('name', ProjectBoard::TESTING_COLUMNS[1])
            ->first();
        $todoColumn = $board->columns()
            ->where('name', ProjectBoard::TESTING_COLUMNS[0])
            ->first();

        $testingCard->update([
            'tester_id' => $testerId,
            'project_board_column_id' => $assignedColumn instanceof ProjectBoardColumn
                && $todoColumn instanceof ProjectBoardColumn
                && $testingCard->project_board_column_id === $todoColumn->id
                ? $assignedColumn->id
                : $testingCard->project_board_column_id,
        ]);

        return redirect()
            ->route('projects.kanban.boards.show', [$project, $board])
            ->with('success', 'Testing card assigned.');
    }

    public function move(
        MoveTestingCardRequest $request,
        Project $project,
        ProjectBoard $board,
        TestingCard $testingCard
    ): RedirectResponse {
        $columnId = $request->integer('column_id');
        $toColumn = $board->columns()->whereKey($columnId)->first();

        if (! $toColumn instanceof ProjectBoardColumn) {
            return back()->with('error', 'The selected column is invalid.');
        }

        $toColumnName = $toColumn->name;

        if ($toColumnName === ProjectBoard::TESTING_COLUMNS[2] && $testingCard->tester_id === null) {
            return back()->with('error', 'Assign a tester before starting testing.');
        }

        $updates = [
            'project_board_column_id' => $toColumn->id,
        ];

        if ($toColumnName === ProjectBoard::TESTING_COLUMNS[2] && $testingCard->started_at === null) {
            $updates['started_at'] = now();
        }

        if ($toColumnName === ProjectBoard::TESTING_COLUMNS[3]) {
            $updates['completed_at'] = now();
        }

        $testingCard->update($updates);

        return redirect()
            ->route('projects.kanban.boards.show', [$project, $board])
            ->with('success', 'Testing card moved.');
    }

    public function result(
        RecordTestingResultRequest $request,
        Project $project,
        ProjectBoard $board,
        TestingCard $testingCard,
        IssueKeyGenerator $issueKeyGenerator
    ): RedirectResponse {
        $resultColumn = $board->columns()
            ->where('name', ProjectBoard::TESTING_COLUMNS[4])
            ->first();

        if (! $resultColumn instanceof ProjectBoardColumn) {
            return back()->with('error', 'The Result column is missing for this board.');
        }

        $validated = $request->validated();
        $userId = $request->user()?->id;

        TestingCard::query()->getConnection()->transaction(function () use (
            $testingCard,
            $resultColumn,
            $validated,
            $board,
            $userId,
            $issueKeyGenerator,
            $project
        ) {
            $testingCard->update([
                'project_board_column_id' => $resultColumn->id,
                'result' => $validated['result'],
                'tested_at' => now(),
                'notes' => $validated['notes'] ?? null,
                'completed_at' => $testingCard->completed_at ?? now(),
            ]);

            $story = $testingCard->story;

            if (! $story) {
                return;
            }

            $developmentBoard = $story->board;

            if (! $developmentBoard) {
                return;
            }

            $developmentBoard->loadMissing('columns');

            if ($validated['result'] === TestingCard::RESULT_PASS) {
                $completedColumn = $developmentBoard->columns()
                    ->where('name', ProjectBoard::DEVELOPMENT_COLUMNS[5])
                    ->first();

                if (! $completedColumn instanceof ProjectBoardColumn) {
                    return;
                }

                $fromColumnId = $story->project_board_column_id;

                $story->update([
                    'project_board_column_id' => $completedColumn->id,
                    'blocker_reason' => null,
                ]);

                StoryStatusHistory::query()->create([
                    'story_id' => $story->id,
                    'from_column_id' => $fromColumnId,
                    'to_column_id' => $completedColumn->id,
                    'moved_by' => $userId,
                    'moved_at' => now(),
                    'reason' => 'Testing passed',
                ]);

                return;
            }

            $blockerColumn = $developmentBoard->columns()
                ->where('name', ProjectBoard::DEVELOPMENT_COLUMNS[4])
                ->first();

            if (! $blockerColumn instanceof ProjectBoardColumn) {
                return;
            }

            $issue = $issueKeyGenerator->next($project);

            Bug::query()->firstOrCreate(
                [
                    'testing_card_id' => $testingCard->id,
                    'title' => $validated['bug_title'],
                ],
                [
                    'project_board_id' => $board->id,
                    'story_id' => $testingCard->story_id,
                    'issue_key' => $issue['issue_key'],
                    'issue_number' => $issue['issue_number'],
                    'description' => $validated['bug_description'] ?? null,
                    'steps_to_reproduce' => $validated['bug_steps'] ?? null,
                    'severity' => $validated['bug_severity'],
                    'status' => Bug::STATUS_OPEN,
                    'assignee_id' => $validated['bug_assignee_id'] ?? null,
                    'reported_by' => $userId,
                    'found_at' => now(),
                ]
            );

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
                'reason' => 'Testing failed',
            ]);
        });

        return redirect()
            ->route('projects.kanban.boards.show', [$project, $board])
            ->with('success', 'Testing result recorded.');
    }
}
