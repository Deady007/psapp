<?php

use App\Models\User;

it('shows terminal auth styling on the login page', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('terminal-auth', false);
    $response->assertSee('terminal-auth-card', false);
    $response->assertSee('terminal-btn', false);
    $response->assertSee('terminal-auth-description', false);
});

it('applies terminal classes on the dashboard layout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('terminal-body', false);
    $response->assertSee('terminal-navbar', false);
});

it('keeps the adminlte theme palette green', function () {
    $css = file_get_contents(public_path('vendor/adminlte/dist/css/jarvis.css'));

    expect($css)
        ->toContain('.terminal-auth')
        ->not->toContain('#00ff75')
        ->and($css)->not->toContain('#17a2b8')
        ->and($css)->not->toContain('#dc3545')
        ->and($css)->not->toContain('#ffc107');
});
