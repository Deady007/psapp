<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\RfpDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RfpDocument>
 */
class RfpDocumentFactory extends Factory
{
    protected $model = RfpDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'requested_by' => User::factory(),
            'status' => fake()->randomElement(RfpDocument::STATUSES),
            'file_name' => fake()->optional()->word().'.docx',
            'file_path' => fake()->optional()->filePath(),
            'started_at' => fake()->optional()->dateTime(),
            'completed_at' => fake()->optional()->dateTime(),
            'failed_at' => fake()->optional()->dateTime(),
            'error_message' => fake()->optional()->sentence(),
        ];
    }
}
