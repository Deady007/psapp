<?php

namespace Database\Seeders;

use App\Models\ProjectBoard;
use Illuminate\Database\Seeder;

class ProjectBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProjectBoard::factory()->create();
    }
}
