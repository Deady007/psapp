<?php

namespace Database\Seeders;

use App\Models\ProjectBoardColumn;
use Illuminate\Database\Seeder;

class ProjectBoardColumnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProjectBoardColumn::factory()->create();
    }
}
