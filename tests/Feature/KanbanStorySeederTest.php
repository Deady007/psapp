<?php

use App\Models\Project;
use App\Models\ProjectBoard;
use Database\Seeders\KanbanSeeder;
use Database\Seeders\KanbanStorySeeder;
use Database\Seeders\RbacSeeder;

it('seeds five development stories per column without duplication', function () {
    $this->seed([
        RbacSeeder::class,
        KanbanSeeder::class,
        KanbanStorySeeder::class,
    ]);

    $this->seed(KanbanStorySeeder::class);

    $project = Project::query()->firstOrFail();
    $developmentBoard = $project->developmentBoard()->with('columns')->firstOrFail();

    foreach (ProjectBoard::DEVELOPMENT_COLUMNS as $columnName) {
        $column = $developmentBoard->columns->firstWhere('name', $columnName);

        expect($column)->not->toBeNull();
        expect($developmentBoard->stories()->where('project_board_column_id', $column->id)->count())->toBe(5);
    }
});

it('seeds three testing stories per column without duplication', function () {
    $this->seed([
        RbacSeeder::class,
        KanbanSeeder::class,
        KanbanStorySeeder::class,
    ]);

    $this->seed(KanbanStorySeeder::class);

    $project = Project::query()->firstOrFail();
    $testingBoard = $project->testingBoard()->with('columns')->firstOrFail();

    foreach (ProjectBoard::TESTING_COLUMNS as $columnName) {
        $column = $testingBoard->columns->firstWhere('name', $columnName);

        expect($column)->not->toBeNull();
        expect($testingBoard->stories()->where('project_board_column_id', $column->id)->count())->toBe(3);
    }
});
