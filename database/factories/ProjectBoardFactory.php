<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectBoard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectBoard>
 */
class ProjectBoardFactory extends Factory
{
    protected $model = ProjectBoard::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement([
            ProjectBoard::TYPE_DEVELOPMENT,
            ProjectBoard::TYPE_TESTING,
        ]);

        return [
            'project_id' => function () {
                return Project::factory()->createQuietly()->id;
            },
            'name' => $type,
            'type' => $type,
            'database_changes' => [],
            'page_mappings' => [],
        ];
    }
}
