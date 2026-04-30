<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use function Pest\Laravel\{postJson, assertAuthenticatedAs, assertGuest};

beforeEach(fn() => RateLimiter::clear('fail-login:test@example.com'));

test('should login successfully with correct credentials', function () {
    /** @var User $user */
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    postJson(route('auth.login'), [
        'email' => 'test@example.com',
        'password' => 'password123',
    ])
        ->assertOk()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email']
        ]);

    assertAuthenticatedAs($user);
    expect(Auth::user()->id)->toBe($user->id);
});

test('should fail login with incorrect password', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    postJson(route('auth.login'), [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ])
        ->assertStatus(401);

    assertGuest();
});

test('should validate required fields on login', function () {
    postJson(route('auth.login'), [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

test('should block login after 5 failed attempts (Rate Limit)', function () {
    $email = 'test@example.com';

    foreach (range(1, 5) as $i) {
        postJson(route('auth.login'), [
            'email' => $email,
            'password' => 'wrong-password'
        ]);
    }

    postJson(route('auth.login'), [
        'email' => $email,
        'password' => 'wrong-password',
    ])
        ->assertStatus(429);
});

test('should clear rate limiter after a successful login', function () {
    $email = 'test@example.com';
    User::factory()->create([
        'email' => $email,
        'password' => 'password123',
    ]);

    postJson(route('auth.login'), ['email' => $email, 'password' => 'wrong']);
    expect(RateLimiter::attempts("fail-login:$email"))->toBe(1);

    postJson(route('auth.login'), ['email' => $email, 'password' => 'password123']);
    expect(RateLimiter::attempts("fail-login:$email"))->toBe(0);
});

test('should fail login if email is invalid', function () {
    postJson(route('auth.login'), [
        'email' => 'invalid-email',
        'password' => 'password123',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('should fail login if user does not exist', function () {
    postJson(route('auth.login'), [
        'email' => 'nonexistent@example.com',
        'password' => 'password123',
    ])
        ->assertStatus(401)
        ->assertJsonPath('message', __('auth.invalid_credentials'));

    assertGuest();
});
