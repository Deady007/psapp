<?php

namespace Database\Factories;

use App\Models\ProjectBoard;
use App\Models\ProjectBoardColumn;
use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Story>
 */
class StoryFactory extends Factory
{
    protected $model = Story::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_board_id' => function () {
                return ProjectBoard::factory()->create([
                    'name' => ProjectBoard::TYPE_DEVELOPMENT,
                    'type' => ProjectBoard::TYPE_DEVELOPMENT,
                ])->id;
            },
            'project_board_column_id' => function (array $attributes) {
                return ProjectBoardColumn::factory()->create([
                    'project_board_id' => $attributes['project_board_id'],
                    'name' => ProjectBoard::DEVELOPMENT_COLUMNS[0],
                    'position' => 1,
                ])->id;
            },
            'title' => fake()->sentence(4),
            'priority' => fake()->randomElement(Story::PRIORITIES),
            'due_date' => fake()->date(),
            'description' => fake()->paragraph(),
            'acceptance_criteria' => fake()->sentence(8),
            'notes' => fake()->sentence(6),
            'assignee_id' => User::factory(),
            'created_by' => User::factory(),
        ];
    }
}
