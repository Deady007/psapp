<?php

namespace Database\Factories;

use App\Models\ProjectBoard;
use App\Models\ProjectBoardColumn;
use App\Models\Story;
use App\Models\TestingCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TestingCard>
 */
class TestingCardFactory extends Factory
{
    protected $model = TestingCard::class;

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
                    'name' => ProjectBoard::TYPE_TESTING,
                    'type' => ProjectBoard::TYPE_TESTING,
                ])->id;
            },
            'project_board_column_id' => function (array $attributes) {
                return ProjectBoardColumn::factory()->create([
                    'project_board_id' => $attributes['project_board_id'],
                    'name' => ProjectBoard::TESTING_COLUMNS[0],
                    'position' => 1,
                ])->id;
            },
            'story_id' => Story::factory(),
            'tester_id' => User::factory(),
            'created_by' => User::factory(),
            'result' => null,
            'tested_at' => null,
            'notes' => fake()->sentence(8),
        ];
    }
}
