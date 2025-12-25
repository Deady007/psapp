<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DemoCustomerProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = now();

        $customer = Customer::query()->firstOrCreate(
            ['name' => 'Demo Customer'],
            ['status' => 'active'],
        );

        if ($customer->status !== 'active') {
            $customer->update(['status' => 'active']);
        }

        $contacts = [
            ['name' => 'Project Lead', 'designation' => 'Lead'],
            ['name' => 'QA Contact', 'designation' => 'QA'],
            ['name' => 'IT Admin', 'designation' => 'Admin'],
        ];

        foreach ($contacts as $contactData) {
            $contact = $customer->contacts()
                ->where('name', $contactData['name'])
                ->first();

            if ($contact === null) {
                $contact = $customer->contacts()->create([
                    'name' => $contactData['name'],
                    'email' => fake()->safeEmail(),
                    'phone' => fake()->phoneNumber(),
                    'designation' => $contactData['designation'],
                ]);
            }

            if ($contact->designation !== $contactData['designation']) {
                $contact->update(['designation' => $contactData['designation']]);
            }
        }

        if ($customer->projects()->doesntExist()) {
            Project::factory()->create([
                'customer_id' => $customer->id,
                'status' => 'active',
                'start_date' => $today->toDateString(),
                'due_date' => $today->copy()->addWeeks(6)->toDateString(),
            ]);
        }
    }
}
