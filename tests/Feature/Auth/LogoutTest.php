<?php

use App\Models\User;
use function Pest\Laravel\{postJson, actingAs, assertGuest};

test('should logout successfully when authenticated', function () {
    /** @var User $user */
    $user = User::factory()->create();

    // Authenticate the user and call logout
    actingAs($user, 'web')
        ->postJson(route('auth.logout'))
        ->assertOk();

    assertGuest();
});

test('should fail logout if user is not authenticated', function () {
    postJson(route('auth.logout'))
        ->assertStatus(401);
});
