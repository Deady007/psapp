<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'name' => fake()->sentence(3),
            'code' => strtoupper(fake()->bothify('PRJ-###')),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(Project::STATUSES),
            'start_date' => fake()->date(),
            'due_date' => fake()->date(),
        ];
    }
}
