<?php

namespace Database\Factories;

use App\Models\Story;
use App\Models\StoryTask;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoryTask>
 */
class StoryTaskFactory extends Factory
{
    protected $model = StoryTask::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'story_id' => Story::factory(),
            'title' => fake()->sentence(5),
            'notes' => fake()->sentence(8),
            'is_completed' => fake()->boolean(25),
            'assignee_id' => User::factory(),
        ];
    }
}
