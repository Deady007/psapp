<?php

namespace Database\Factories;

use App\Models\ProjectBoardColumn;
use App\Models\Story;
use App\Models\StoryStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoryStatusHistory>
 */
class StoryStatusHistoryFactory extends Factory
{
    protected $model = StoryStatusHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'story_id' => Story::factory(),
            'from_column_id' => ProjectBoardColumn::factory(),
            'to_column_id' => ProjectBoardColumn::factory(),
            'moved_by' => User::factory(),
            'moved_at' => now(),
            'reason' => fake()->sentence(3),
            'notes' => fake()->sentence(),
        ];
    }
}
