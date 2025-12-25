<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectBoard;
use App\Models\ProjectBoardColumn;

class ProjectBoardProvisioner
{
    public function ensureBoards(Project $project): void
    {
        foreach (ProjectBoard::columnDefinitions() as $type => $columns) {
            $board = $project->boards()->firstOrCreate(
                ['type' => $type],
                [
                    'name' => $type,
                    'database_changes' => [],
                    'page_mappings' => [],
                ]
            );

            if ($board->name !== $type) {
                $board->update(['name' => $type]);
            }

            $this->ensureColumns($board, $columns);
        }
    }

    /**
     * @param  list<string>  $columns
     */
    private function ensureColumns(ProjectBoard $board, array $columns): void
    {
        $existing = $board->columns()->get()->keyBy('name');

        foreach ($columns as $index => $name) {
            $position = $index + 1;
            $column = $existing->get($name);

            if ($column instanceof ProjectBoardColumn) {
                if ($column->position !== $position) {
                    $column->update(['position' => $position]);
                }

                continue;
            }

            $board->columns()->create([
                'name' => $name,
                'position' => $position,
            ]);
        }
    }
}
