<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Project;
use App\Services\ProjectBoardProvisioner;
use Illuminate\Database\Seeder;

class KanbanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = Customer::query()->firstOrCreate(
            ['name' => 'Beach Customer'],
            ['status' => 'active']
        );

        if ($customer->status !== 'active') {
            $customer->update(['status' => 'active']);
        }

        Project::query()->firstOrCreate(
            ['name' => 'Beach'],
            [
                'customer_id' => $customer->id,
                'status' => 'active',
                'start_date' => now()->toDateString(),
                'due_date' => now()->addWeeks(6)->toDateString(),
            ]
        );

        $provisioner = app(ProjectBoardProvisioner::class);

        Project::query()->chunkById(100, function ($projects) use ($provisioner) {
            $projects->each(function (Project $project) use ($provisioner) {
                $provisioner->ensureBoards($project);
            });
        });
    }
}
