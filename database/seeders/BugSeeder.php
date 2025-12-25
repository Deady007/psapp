<?php

namespace Database\Seeders;

use App\Models\Bug;
use Illuminate\Database\Seeder;

class BugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bug::factory()->create();
    }
}
