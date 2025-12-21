<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'folder_id' => function (array $attributes) {
                return DocumentFolder::factory()->create([
                    'project_id' => $attributes['project_id'],
                ])->id;
            },
            'drive_file_id' => fake()->uuid(),
            'name' => fake()->word().'.pdf',
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(1000, 500000),
            'source' => fake()->randomElement(['Email', 'Portal', 'In Person']),
            'received_from' => fake()->name(),
            'received_at' => fake()->date(),
            'version' => 1,
            'checksum' => hash('md5', fake()->uuid()),
            'uploaded_by' => User::factory(),
        ];
    }
}
