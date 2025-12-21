<?php

use App\Models\Customer;
use App\Models\Product;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

beforeEach(function () {
    $this->seed(RbacSeeder::class);
    $this->withoutMiddleware(ValidateCsrfToken::class);
});

function createProjectUser(): User
{
    $user = User::factory()->create();
    $user->assignRole('admin');

    return $user;
}

it('syncs products when creating a project', function () {
    $user = createProjectUser();
    $customer = Customer::factory()->create();
    $products = Product::factory()->count(2)->create();

    $payload = [
        'customer_id' => $customer->id,
        'name' => 'Quality Implementation',
        'code' => 'PRJ-9001',
        'description' => 'Initial rollout.',
        'status' => Project::STATUSES[0],
        'start_date' => '2025-01-01',
        'due_date' => '2025-01-15',
        'products' => $products->pluck('id')->all(),
    ];

    $this->actingAs($user)
        ->post(route('projects.store'), $payload)
        ->assertRedirect();

    $project = Project::query()->where('code', 'PRJ-9001')->first();

    expect($project)->not->toBeNull();
    expect($project->products)->toHaveCount(2);
});

it('syncs products when updating a project', function () {
    $user = createProjectUser();
    $customer = Customer::factory()->create();
    $productA = Product::factory()->create();
    $productB = Product::factory()->create();

    $project = Project::factory()->create([
        'customer_id' => $customer->id,
    ]);
    $project->products()->sync([$productA->id]);

    $payload = [
        'customer_id' => $project->customer_id,
        'name' => $project->name,
        'code' => $project->code,
        'description' => $project->description,
        'status' => $project->status,
        'start_date' => '2025-02-01',
        'due_date' => '2025-02-10',
        'products' => [$productB->id],
    ];

    $this->actingAs($user)
        ->put(route('projects.update', $project), $payload)
        ->assertRedirect(route('projects.show', $project));

    $project->refresh();

    expect($project->products->pluck('id'))->toContain($productB->id)
        ->not->toContain($productA->id);
});

it('validates product ids on project store', function () {
    $user = createProjectUser();
    $customer = Customer::factory()->create();

    $payload = [
        'customer_id' => $customer->id,
        'name' => 'Invalid Products',
        'code' => 'PRJ-9002',
        'description' => 'Invalid product test.',
        'status' => Project::STATUSES[0],
        'start_date' => '2025-02-01',
        'due_date' => '2025-02-10',
        'products' => [999999],
    ];

    $this->actingAs($user)
        ->post(route('projects.store'), $payload)
        ->assertSessionHasErrors(['products.0']);
});

it('forbids project access for users without roles', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('projects.index'))
        ->assertForbidden();
});
