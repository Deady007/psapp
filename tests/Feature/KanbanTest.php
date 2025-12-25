<?php

use App\Models\BoardDocument;
use App\Models\Bug;
use App\Models\Project;
use App\Models\ProjectBoard;
use App\Models\ProjectBoardColumn;
use App\Models\Story;
use App\Models\TestingCard;
use App\Models\User;
use Database\Seeders\KanbanSeeder;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RbacSeeder::class);
    $this->withoutMiddleware(ValidateCsrfToken::class);
});

function createDeveloperUser(): User
{
    $user = User::factory()->create();
    $user->assignRole('developer');

    return $user;
}

function createTesterUser(): User
{
    $user = User::factory()->create();
    $user->assignRole('tester');

    return $user;
}

function developmentBoard(Project $project): ProjectBoard
{
    return $project->boards()->where('type', ProjectBoard::TYPE_DEVELOPMENT)->firstOrFail();
}

function testingBoard(Project $project): ProjectBoard
{
    return $project->boards()->where('type', ProjectBoard::TYPE_TESTING)->firstOrFail();
}

function boardColumn(ProjectBoard $board, string $name): ProjectBoardColumn
{
    return $board->columns()->where('name', $name)->firstOrFail();
}

dataset('invalidStoryData', [
    'missing title' => [['title' => null], ['title']],
    'missing due date' => [['due_date' => null], ['due_date']],
    'missing priority' => [['priority' => null], ['priority']],
]);

it('auto-creates development and testing boards on project creation', function () {
    $project = Project::factory()->create();

    $types = $project->boards()->pluck('type')->all();

    expect($types)->toEqualCanonicalizing([
        ProjectBoard::TYPE_DEVELOPMENT,
        ProjectBoard::TYPE_TESTING,
    ]);

    $development = developmentBoard($project);
    expect($development->columns()->count())->toBe(count(ProjectBoard::DEVELOPMENT_COLUMNS));
});

it('seeds beach project boards idempotently', function () {
    $this->seed(KanbanSeeder::class);
    $this->seed(KanbanSeeder::class);

    $project = Project::query()->where('name', 'Beach')->first();

    expect($project)->not->toBeNull();
    expect($project->boards()->count())->toBe(2);
    expect($project->boards()->pluck('type')->all())->toEqualCanonicalizing([
        ProjectBoard::TYPE_DEVELOPMENT,
        ProjectBoard::TYPE_TESTING,
    ]);
});

it('validates story creation input', function (array $override, array $errors) {
    $user = createDeveloperUser();
    $project = Project::factory()->create();
    $board = developmentBoard($project);

    $payload = [
        'title' => 'Build login flow',
        'description' => 'Add login screen and API.',
        'acceptance_criteria' => 'Users can sign in with email.',
        'notes' => 'Focus on validation.',
        'assignee_id' => $user->id,
        'due_date' => now()->addWeek()->toDateString(),
        'priority' => Story::PRIORITY_MEDIUM,
    ];

    $this->actingAs($user)
        ->post(route('projects.kanban.boards.stories.store', [$project, $board]), array_merge($payload, $override))
        ->assertSessionHasErrors($errors);
})->with('invalidStoryData');

it('prevents assigning non-developer users on story creation', function () {
    $user = createDeveloperUser();
    $tester = createTesterUser();
    $project = Project::factory()->create();
    $board = developmentBoard($project);

    $payload = [
        'title' => 'Build login flow',
        'description' => 'Add login screen and API.',
        'acceptance_criteria' => 'Users can sign in with email.',
        'notes' => 'Focus on validation.',
        'assignee_id' => $tester->id,
        'due_date' => now()->addWeek()->toDateString(),
        'priority' => Story::PRIORITY_MEDIUM,
    ];

    $this->actingAs($user)
        ->post(route('projects.kanban.boards.stories.store', [$project, $board]), $payload)
        ->assertSessionHasErrors(['assignee_id']);
});

it('creates stories successfully', function () {
    $user = createDeveloperUser();
    $project = Project::factory()->create();
    $board = developmentBoard($project);

    $payload = [
        'title' => 'Build login flow',
        'description' => 'Add login screen and API.',
        'acceptance_criteria' => 'Users can sign in with email.',
        'notes' => 'Focus on validation.',
        'assignee_id' => $user->id,
        'due_date' => now()->addWeek()->toDateString(),
        'priority' => Story::PRIORITY_MEDIUM,
    ];

    $this->actingAs($user)
        ->post(route('projects.kanban.boards.stories.store', [$project, $board]), $payload)
        ->assertRedirect(route('projects.kanban.boards.show', [$project, $board]));

    $this->assertDatabaseHas('stories', [
        'project_board_id' => $board->id,
        'title' => 'Build login flow',
        'assignee_id' => $user->id,
    ]);
});

it('validates story assignment to developers', function () {
    $user = createDeveloperUser();
    $tester = createTesterUser();
    $project = Project::factory()->create();
    $board = developmentBoard($project);
    $todoColumn = boardColumn($board, ProjectBoard::DEVELOPMENT_COLUMNS[0]);
    $story = $board->stories()->create([
        'project_board_column_id' => $todoColumn->id,
        'title' => 'Build login flow',
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->post(route('projects.kanban.boards.stories.assign', [$project, $board, $story]), [
            'assignee_id' => $tester->id,
        ])
        ->assertSessionHasErrors(['assignee_id']);
});

it('assigns stories successfully', function () {
    $user = createDeveloperUser();
    $developer = createDeveloperUser();
    $project = Project::factory()->create();
    $board = developmentBoard($project);
    $todoColumn = boardColumn($board, ProjectBoard::DEVELOPMENT_COLUMNS[0]);
    $story = $board->stories()->create([
        'project_board_column_id' => $todoColumn->id,
        'title' => 'Build login flow',
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->post(route('projects.kanban.boards.stories.assign', [$project, $board, $story]), [
            'assignee_id' => $developer->id,
        ])
        ->assertRedirect(route('projects.kanban.boards.show', [$project, $board]));

    expect($story->refresh()->assignee_id)->toBe($developer->id);
});

it('moves stories across columns and records history', function () {
    $user = createDeveloperUser();
    $project = Project::factory()->create();
    $board = developmentBoard($project);
    $todoColumn = boardColumn($board, ProjectBoard::DEVELOPMENT_COLUMNS[0]);
    $targetColumn = boardColumn($board, ProjectBoard::DEVELOPMENT_COLUMNS[2]);
    $story = $board->stories()->create([
        'project_board_column_id' => $todoColumn->id,
        'title' => 'Build login flow',
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->post(route('projects.kanban.boards.stories.move', [$project, $board, $story]), [
            'column_id' => $targetColumn->id,
        ])
        ->assertRedirect(route('projects.kanban.boards.show', [$project, $board]));

    expect($story->refresh()->project_board_column_id)->toBe($targetColumn->id);

    $this->assertDatabaseHas('story_status_histories', [
        'story_id' => $story->id,
        'from_column_id' => $todoColumn->id,
        'to_column_id' => $targetColumn->id,
    ]);
});

it('creates a bug when testing fails', function () {
    $developer = createDeveloperUser();
    $tester = createTesterUser();
    $project = Project::factory()->create();
    $developmentBoard = developmentBoard($project);
    $reviewColumn = boardColumn($developmentBoard, ProjectBoard::DEVELOPMENT_COLUMNS[3]);

    $story = $developmentBoard->stories()->create([
        'project_board_column_id' => $reviewColumn->id,
        'title' => 'Build login flow',
        'description' => 'Add login screen and API.',
        'database_changes_confirmed' => true,
        'page_mappings_confirmed' => true,
        'created_by' => $developer->id,
    ]);

    $this->actingAs($developer)
        ->post(route('projects.kanban.boards.stories.send-to-testing', [$project, $developmentBoard, $story]), [
            'tester_id' => $tester->id,
        ])
        ->assertRedirect(route('projects.kanban.boards.show', [$project, $developmentBoard]));

    $testingBoard = testingBoard($project);
    $testingCard = TestingCard::query()->where('story_id', $story->id)->firstOrFail();

    $this->actingAs($tester)
        ->post(route('projects.kanban.boards.testing-cards.result', [$project, $testingBoard, $testingCard]), [
            'result' => TestingCard::RESULT_FAIL,
            'bug_title' => 'Login fails',
            'bug_severity' => Bug::SEVERITY_HIGH,
            'bug_description' => 'Authentication returns 500.',
        ])
        ->assertRedirect(route('projects.kanban.boards.show', [$project, $testingBoard]));

    $this->assertDatabaseHas('bugs', [
        'story_id' => $story->id,
        'testing_card_id' => $testingCard->id,
        'title' => 'Login fails',
        'status' => Bug::STATUS_OPEN,
    ]);
});

it('generates a user manual from development data', function () {
    Storage::fake('local');

    $user = createDeveloperUser();
    $project = Project::factory()->create();
    $board = developmentBoard($project);
    $todoColumn = boardColumn($board, ProjectBoard::DEVELOPMENT_COLUMNS[0]);
    $board->stories()->create([
        'project_board_column_id' => $todoColumn->id,
        'title' => 'Build login flow',
        'acceptance_criteria' => 'Users can sign in.',
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->post(route('projects.kanban.boards.documents.store', [$project, $board]), [
            'type' => BoardDocument::TYPE_USER_MANUAL,
        ])
        ->assertRedirect(route('projects.kanban.boards.show', [$project, $board]));

    $document = BoardDocument::query()->where('project_board_id', $board->id)->firstOrFail();

    expect($document->content)->toContain('User Manual');
    Storage::disk('local')->assertExists($document->storage_path);
});

it('generates a validation report from testing outcomes', function () {
    Storage::fake('local');

    $user = createDeveloperUser();
    $tester = createTesterUser();
    $project = Project::factory()->create();
    $developmentBoard = developmentBoard($project);
    $todoColumn = boardColumn($developmentBoard, ProjectBoard::DEVELOPMENT_COLUMNS[0]);
    $story = $developmentBoard->stories()->create([
        'project_board_column_id' => $todoColumn->id,
        'title' => 'Build login flow',
        'created_by' => $user->id,
    ]);

    $testingBoard = testingBoard($project);
    $testingColumn = boardColumn($testingBoard, ProjectBoard::TESTING_COLUMNS[0]);
    TestingCard::query()->create([
        'project_board_id' => $testingBoard->id,
        'project_board_column_id' => $testingColumn->id,
        'story_id' => $story->id,
        'tester_id' => $tester->id,
        'result' => TestingCard::RESULT_PASS,
        'tested_at' => now(),
    ]);

    $this->actingAs($tester)
        ->post(route('projects.kanban.boards.documents.store', [$project, $testingBoard]), [
            'type' => BoardDocument::TYPE_VALIDATION_REPORT,
        ])
        ->assertRedirect(route('projects.kanban.boards.show', [$project, $testingBoard]));

    $document = BoardDocument::query()->where('project_board_id', $testingBoard->id)->firstOrFail();

    expect($document->content)->toContain('Validation Report')
        ->and($document->content)->toContain('Pass: 1');

    Storage::disk('local')->assertExists($document->storage_path);
});
