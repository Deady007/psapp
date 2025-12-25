<?php

namespace Database\Seeders;

use App\Models\TestingCard;
use Illuminate\Database\Seeder;

class TestingCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TestingCard::factory()->create();
    }
}
