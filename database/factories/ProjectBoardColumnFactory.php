<?php

namespace Database\Factories;

use App\Models\ProjectBoard;
use App\Models\ProjectBoardColumn;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectBoardColumn>
 */
class ProjectBoardColumnFactory extends Factory
{
    protected $model = ProjectBoardColumn::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_board_id' => ProjectBoard::factory(),
            'name' => fake()->words(2, true),
            'position' => fake()->numberBetween(1, 8),
        ];
    }
}
