<?php

use App\Models\User;
use Database\Seeders\RbacSeeder;

it('seeds workflow users for each role without duplication', function () {
    $this->seed(RbacSeeder::class);
    $this->seed(RbacSeeder::class);

    $expectedUsers = [
        'parmarviral397@gmail.com' => 'admin',
        'user@example.com' => 'user',
        'developer@example.com' => 'developer',
        'tester@example.com' => 'tester',
    ];

    expect(User::query()->count())->toBe(4);

    foreach ($expectedUsers as $email => $role) {
        $user = User::query()->where('email', $email)->first();

        expect($user)->not->toBeNull();
        expect($user->hasRole($role))->toBeTrue();
    }
});
