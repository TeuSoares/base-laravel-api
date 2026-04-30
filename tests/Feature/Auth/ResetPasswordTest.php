<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use function Pest\Laravel\{postJson};

test('should reset password successfully with valid token', function () {
    /** @var User $user */
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => 'OldPassword123!',
    ]);

    /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
    $broker = Password::broker();
    $token = $broker->createToken($user);

    $response = postJson(route('auth.reset-password'), [
        'token' => $token,
        'email' => 'user@example.com',
        'password' => 'SafePass123!',
        'password_confirmation' => 'SafePass123!',
    ]);

    $response->assertOk()
        ->assertJsonPath('message', __('auth.password_reset_success'));

    expect(Hash::check('SafePass123!', $user->refresh()->password))->toBeTrue();
});

test('should fail reset if token is invalid', function () {
    $user = User::factory()->create(['email' => 'user@example.com']);

    postJson(route('auth.reset-password'), [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'SafePass123!',
        'password_confirmation' => 'SafePass123!',
    ])
        ->assertStatus(422)
        ->assertJsonPath('message', __('passwords.token'));
});

test('should validate password requirements', function () {
    /** @var User $user */
    $user = User::factory()->create(['email' => 'user@example.com']);

    /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
    $broker = Password::broker();
    $token = $broker->createToken($user);

    postJson(route('auth.reset-password'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'simple',
        'password_confirmation' => 'simple',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('should fail if email does not exist', function () {
    /** @var User $user */
    $user = User::factory()->create(['email' => 'user@example.com']);

    /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
    $broker = Password::broker();
    $token = $broker->createToken($user);

    postJson(route('auth.reset-password'), [
        'token' => $token,
        'email' => 'wrong@example.com',
        'password' => 'SafePass123!',
        'password_confirmation' => 'SafePass123!',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('should fail if passwords do not match during reset', function () {
    /** @var User $user */
    $user = User::factory()->create(['email' => 'user@example.com']);

    /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
    $broker = Password::broker();
    $token = $broker->createToken($user);

    $response = postJson(route('auth.reset-password'), [
        'token' => $token,
        'email' => 'user@example.com',
        'password' => 'NewPassword123!',
        'password_confirmation' => 'DifferentPassword123!',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});
