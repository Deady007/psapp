<?php

use App\Models\Project;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

beforeEach(function () {
    $this->seed(RbacSeeder::class);
    $this->withoutMiddleware(ValidateCsrfToken::class);
});

function createAdminUser(): User
{
    $user = User::factory()->create();
    $user->assignRole('admin');

    return $user;
}

it('renders bootstrap pagination links', function () {
    $user = createAdminUser();
    Project::factory()->count(11)->create();

    $response = $this->actingAs($user)
        ->get(route('projects.index'));

    $response->assertSuccessful()
        ->assertSee('page-item', false)
        ->assertSee('page-link', false);
});
