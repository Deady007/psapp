<?php

namespace Database\Seeders;

use App\Models\ProjectKickoff;
use App\Models\ProjectKickoffStakeholder;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectKickoffStakeholderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kickoff = ProjectKickoff::query()->first();

        if ($kickoff === null) {
            return;
        }

        $user = User::query()->first() ?? User::factory()->create();

        ProjectKickoffStakeholder::firstOrCreate([
            'project_kickoff_id' => $kickoff->id,
            'stakeholder_type' => User::class,
            'stakeholder_id' => $user->id,
        ]);
    }
}
