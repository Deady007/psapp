<?php

namespace Database\Seeders;

use App\Models\StoryTask;
use Illuminate\Database\Seeder;

class StoryTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StoryTask::factory()->create();
    }
}
