<?php

use App\Models\Project;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

beforeEach(function () {
    $this->seed(RbacSeeder::class);
    $this->withoutMiddleware(ValidateCsrfToken::class);
});

dataset('legacyProjectDocumentsRoutes', [
    'index' => ['get', '/projects/%s/documents'],
    'create' => ['get', '/projects/%s/documents/create'],
    'store' => ['post', '/projects/%s/documents'],
    'edit' => ['get', '/projects/%s/documents/1/edit'],
    'update' => ['put', '/projects/%s/documents/1'],
    'destroy' => ['delete', '/projects/%s/documents/1'],
    'download' => ['get', '/projects/%s/documents/1/download'],
]);

it('returns not found for legacy project document routes', function (string $method, string $uriTemplate) {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $project = Project::factory()->create();
    $uri = sprintf($uriTemplate, $project->id);

    $response = match ($method) {
        'get' => $this->actingAs($user)->get($uri),
        'post' => $this->actingAs($user)->post($uri, []),
        'put' => $this->actingAs($user)->put($uri, []),
        'delete' => $this->actingAs($user)->delete($uri),
        default => $this->actingAs($user)->get($uri),
    };

    $response->assertNotFound();
})->with('legacyProjectDocumentsRoutes');
