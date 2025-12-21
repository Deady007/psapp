<?php

namespace Database\Seeders;

use App\Models\Contact;
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
        $customer = Customer::factory()->create([
            'name' => 'Demo Customer',
            'status' => 'active',
        ]);

        $contacts = [
            ['name' => 'Project Lead', 'designation' => 'Lead'],
            ['name' => 'QA Contact', 'designation' => 'QA'],
            ['name' => 'IT Admin', 'designation' => 'Admin'],
        ];

        foreach ($contacts as $contactData) {
            Contact::query()->create([
                'customer_id' => $customer->id,
                'name' => $contactData['name'],
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'designation' => $contactData['designation'],
            ]);
        }

        Project::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'active',
            'start_date' => now()->toDateString(),
            'due_date' => now()->addWeeks(6)->toDateString(),
        ]);
    }
}
