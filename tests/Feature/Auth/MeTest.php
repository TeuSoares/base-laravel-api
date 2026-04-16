<?php

use App\Models\User;
use function Pest\Laravel\{getJson, actingAs};

test('should return authenticated user data', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user)
        ->getJson(route('auth.me'))
        ->assertOk()
        ->assertJson([
            'data' => [
                'id'    => $user->id,
                'email' => $user->email,
            ],
        ]);
});

test('should fail if user is not authenticated', function () {
    getJson(route('auth.me'))
        ->assertStatus(401);
});
