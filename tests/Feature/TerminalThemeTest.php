<?php

use App\Models\User;

it('shows terminal auth styling on the login page', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('terminal-auth-card', false);
    $response->assertSee('terminal-btn', false);
});

it('applies terminal classes on the dashboard layout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('terminal-body', false);
    $response->assertSee('terminal-navbar', false);
});
