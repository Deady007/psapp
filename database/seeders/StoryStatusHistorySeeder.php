<?php

namespace Database\Seeders;

use App\Models\StoryStatusHistory;
use Illuminate\Database\Seeder;

class StoryStatusHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StoryStatusHistory::factory()->create();
    }
}
