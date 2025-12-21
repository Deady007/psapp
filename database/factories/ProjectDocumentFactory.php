<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectDocument>
 */
class ProjectDocumentFactory extends Factory
{
    protected $model = ProjectDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'category' => fake()->randomElement(['Job Card', 'Test Request Form', 'Analysis Report']),
            'original_name' => fake()->word().'.pdf',
            'path' => 'project-documents/'.fake()->uuid().'/sample.pdf',
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(1000, 500000),
            'notes' => fake()->sentence(),
            'uploaded_by' => User::factory(),
            'collected_at' => fake()->date(),
        ];
    }
}
