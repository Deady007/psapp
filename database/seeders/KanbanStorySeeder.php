<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectBoard;
use App\Models\Story;
use App\Models\User;
use App\Services\ProjectBoardProvisioner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class KanbanStorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $developerRole = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $testerRole = Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);

        $developer = $this->ensureUserWithRole(
            'developer@example.com',
            'Developer',
            'Developer@12345',
            $developerRole
        );

        $tester = $this->ensureUserWithRole(
            'tester@example.com',
            'Tester',
            'Tester@12345',
            $testerRole
        );

        $provisioner = app(ProjectBoardProvisioner::class);

        Project::query()->chunkById(50, function (Collection $projects) use ($provisioner, $developer, $tester) {
            $projects->each(function (Project $project) use ($provisioner, $developer, $tester) {
                $provisioner->ensureBoards($project);

                $project->loadMissing(['developmentBoard.columns', 'testingBoard.columns']);

                if ($project->developmentBoard !== null) {
                    $this->seedStoriesForBoard(
                        $project->developmentBoard,
                        ProjectBoard::DEVELOPMENT_COLUMNS,
                        5,
                        $developer
                    );
                }

                if ($project->testingBoard !== null) {
                    $this->seedStoriesForBoard(
                        $project->testingBoard,
                        ProjectBoard::TESTING_COLUMNS,
                        3,
                        $tester
                    );
                }
            });
        });
    }

    private function ensureUserWithRole(
        string $email,
        string $name,
        string $password,
        Role $role
    ): User {
        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);
        }

        if (! $user->hasRole($role->name)) {
            $user->assignRole($role);
        }

        return $user;
    }

    /**
     * @param  list<string>  $columns
     */
    private function seedStoriesForBoard(
        ProjectBoard $board,
        array $columns,
        int $targetPerColumn,
        User $owner
    ): void {
        $columnMap = $board->columns()->get()->keyBy('name');

        foreach ($columns as $name) {
            $column = $columnMap->get($name);

            if ($column === null) {
                continue;
            }

            $currentCount = $board->stories()
                ->where('project_board_column_id', $column->id)
                ->count();

            $remaining = $targetPerColumn - $currentCount;

            if ($remaining <= 0) {
                continue;
            }

            Story::factory()
                ->count($remaining)
                ->create([
                    'project_board_id' => $board->id,
                    'project_board_column_id' => $column->id,
                    'assignee_id' => $owner->id,
                    'created_by' => $owner->id,
                    'priority' => Story::PRIORITY_MEDIUM,
                    'title' => sprintf('%s - %s story', $board->type, $name),
                ]);
        }
    }
}
