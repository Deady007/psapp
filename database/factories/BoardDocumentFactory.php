<?php

namespace Database\Factories;

use App\Models\BoardDocument;
use App\Models\ProjectBoard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BoardDocument>
 */
class BoardDocumentFactory extends Factory
{
    protected $model = BoardDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_board_id' => ProjectBoard::factory(),
            'type' => fake()->randomElement(BoardDocument::TYPES),
            'content' => fake()->paragraphs(3, true),
            'storage_path' => sprintf('kanban/%s.md', fake()->uuid()),
            'file_name' => sprintf('%s.md', fake()->slug(3)),
            'generated_by' => User::factory(),
            'generated_at' => now(),
        ];
    }
}
