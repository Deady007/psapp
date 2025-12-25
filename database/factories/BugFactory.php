<?php

namespace Database\Factories;

use App\Models\Bug;
use App\Models\TestingCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bug>
 */
class BugFactory extends Factory
{
    protected $model = Bug::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'testing_card_id' => TestingCard::factory(),
            'project_board_id' => function (array $attributes) {
                return TestingCard::query()->find($attributes['testing_card_id'])->project_board_id;
            },
            'story_id' => function (array $attributes) {
                return TestingCard::query()->find($attributes['testing_card_id'])->story_id;
            },
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'severity' => fake()->randomElement(Bug::SEVERITIES),
            'steps_to_reproduce' => fake()->sentence(),
            'status' => fake()->randomElement(Bug::STATUSES),
            'assignee_id' => User::factory(),
            'reported_by' => User::factory(),
        ];
    }
}
