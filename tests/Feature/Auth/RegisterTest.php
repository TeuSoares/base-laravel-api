<?php

use App\Models\User;
use function Pest\Laravel\{postJson, assertDatabaseHas, assertAuthenticated};

function validRegistrationPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Test User',
        'email' => 'register@example.com',
        'country_code' => 'BR',
        'password' => 'SafePass123!',
        'password_confirmation' => 'SafePass123!',
    ], $overrides);
}

test('should register a new user successfully with all valid fields', function () {
    $userData = validRegistrationPayload();

    $response = postJson(route('auth.register'), $userData);

    $response->assertStatus(201)
        ->assertJsonPath('message', __('auth.register_success'))
        ->assertJsonStructure(['data' => ['id', 'name', 'email']]);

    assertDatabaseHas('users', [
        'email' => 'register@example.com',
        'country_code' => 'BR',
    ]);

    assertAuthenticated();
});

test('should fail registration if email is already taken', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $payload = validRegistrationPayload(['email' => 'existing@example.com']);

    postJson(route('auth.register'), $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('password must have at least 10 characters', function () {
    $payload = validRegistrationPayload([
        'password' => 'Short12',
        'password_confirmation' => 'Short12'
    ]);

    postJson(route('auth.register'), $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('password must contain at least one letter and one number', function () {
    postJson(route('auth.register'), validRegistrationPayload([
        'password' => '12345678910',
        'password_confirmation' => '12345678910'
    ]))->assertJsonValidationErrors(['password']);

    postJson(route('auth.register'), validRegistrationPayload([
        'password' => 'OnlyLettersPass',
        'password_confirmation' => 'OnlyLettersPass'
    ]))->assertJsonValidationErrors(['password']);
});

test('should block compromised passwords even if they meet length requirements', function () {
    $payload = validRegistrationPayload([
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ]);

    postJson(route('auth.register'), $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('should fail if password confirmation does not match', function () {
    $payload = validRegistrationPayload([
        'password' => 'ValidPass123',
        'password_confirmation' => 'WrongConfirmation'
    ]);

    postJson(route('auth.register'), $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('should fail if country code is not a valid enum value', function () {
    $payload = validRegistrationPayload(['country_code' => 'XX']);

    postJson(route('auth.register'), $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['country_code']);
});
