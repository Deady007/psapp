<?php

namespace Database\Seeders;

use App\Models\BoardDocument;
use Illuminate\Database\Seeder;

class BoardDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BoardDocument::factory()->create();
    }
}
