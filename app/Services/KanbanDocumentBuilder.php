<?php

namespace App\Services;

use App\Models\BoardDocument;
use App\Models\ProjectBoard;
use App\Models\TestingCard;
use Illuminate\Support\Str;

class KanbanDocumentBuilder
{
    /**
     * @return array{content: string, file_name: string}
     */
    public function build(ProjectBoard $board, string $type): array
    {
        return match ($type) {
            BoardDocument::TYPE_USER_MANUAL => $this->buildUserManual($board),
            BoardDocument::TYPE_VALIDATION_REPORT => $this->buildValidationReport($board),
            default => $this->buildUserManual($board),
        };
    }

    /**
     * @return array{content: string, file_name: string}
     */
    private function buildUserManual(ProjectBoard $board): array
    {
        $board->loadMissing('project');

        $stories = $board->stories()
            ->with('tasks')
            ->orderBy('created_at')
            ->get();

        $lines = [
            '# User Manual',
            sprintf('Project: %s', $board->project->name),
            sprintf('Module: %s', $board->name),
            sprintf('Generated: %s', now()->toDateTimeString()),
            '',
            '## Stories',
        ];

        if ($stories->isEmpty()) {
            $lines[] = '- No stories available.';
        } else {
            foreach ($stories as $story) {
                $lines[] = '';
                $storyKey = $story->issue_key ?: sprintf('Story-%s', $story->id);
                $lines[] = sprintf('### %s - %s', $storyKey, $story->title);

                if ($story->priority) {
                    $lines[] = sprintf('- Priority: %s', $story->priority);
                }

                if ($story->due_date) {
                    $lines[] = sprintf('- Due Date: %s', $story->due_date->toDateString());
                }

                if ($story->description) {
                    $lines[] = sprintf('- Description: %s', $story->description);
                }

                if ($story->acceptance_criteria) {
                    $lines[] = sprintf('- Acceptance Criteria: %s', $story->acceptance_criteria);
                }

                if ($story->notes) {
                    $lines[] = sprintf('- Notes: %s', $story->notes);
                }

                if ($story->tasks->isNotEmpty()) {
                    $lines[] = '- Tasks:';

                    foreach ($story->tasks as $task) {
                        $status = $task->is_completed ? '[x]' : '[ ]';
                        $lines[] = sprintf('  - %s %s', $status, $task->title);
                    }
                }
            }
        }

        $fileName = sprintf(
            'user-manual-%s-%s.md',
            Str::slug($board->project->name),
            now()->format('YmdHis')
        );

        return [
            'content' => implode(PHP_EOL, $lines),
            'file_name' => $fileName,
        ];
    }

    /**
     * @return array{content: string, file_name: string}
     */
    private function buildValidationReport(ProjectBoard $board): array
    {
        $board->loadMissing('project');

        $testingCards = $board->testingCards()
            ->with(['story', 'bugs'])
            ->orderBy('created_at')
            ->get();

        $testedCards = $testingCards->filter(fn (TestingCard $card) => $card->result !== null);
        $passCount = $testedCards->where('result', TestingCard::RESULT_PASS)->count();
        $failCount = $testedCards->where('result', TestingCard::RESULT_FAIL)->count();
        $pendingCount = $testingCards->where('result', null)->count();
        $finalResult = $failCount > 0 ? 'Fail' : 'Pass';

        $lines = [
            '# Validation Report',
            sprintf('Project: %s', $board->project->name),
            sprintf('Module: %s', $board->name),
            sprintf('Generated: %s', now()->toDateTimeString()),
            '',
            '## Summary',
            sprintf('- Total testing cards: %d', $testingCards->count()),
            sprintf('- Tested stories: %d', $testedCards->count()),
            sprintf('- Pass: %d', $passCount),
            sprintf('- Fail: %d', $failCount),
            sprintf('- Pending: %d', $pendingCount),
            sprintf('- Final Result: %s', $finalResult),
            '',
            '## Tested Stories',
        ];

        if ($testedCards->isEmpty()) {
            $lines[] = '- No tested stories available.';
        } else {
            foreach ($testedCards as $card) {
                $resultLabel = $card->result === TestingCard::RESULT_PASS ? 'Pass' : 'Fail';
                $storyKey = $card->story?->issue_key ?: sprintf('Story-%s', $card->story?->id);
                $lines[] = sprintf('- %s - %s (%s)', $storyKey, $card->story?->title, $resultLabel);
            }
        }

        $bugs = $board->bugs()
            ->with('story')
            ->orderBy('created_at')
            ->get();

        $lines[] = '';
        $lines[] = '## Linked Bugs';

        if ($bugs->isEmpty()) {
            $lines[] = '- No bugs linked to testing failures.';
        } else {
            foreach ($bugs as $bug) {
                $storyTitle = $bug->story?->title ?: 'Unknown Story';
                $lines[] = sprintf('- %s (Story: %s, Status: %s)', $bug->title, $storyTitle, $bug->status);
            }
        }

        $fileName = sprintf(
            'validation-report-%s-%s.md',
            Str::slug($board->project->name),
            now()->format('YmdHis')
        );

        return [
            'content' => implode(PHP_EOL, $lines),
            'file_name' => $fileName,
        ];
    }
}
