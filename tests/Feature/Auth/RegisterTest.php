<?php

use App\Models\User;
use App\Modules\Auth\Enums\DocumentType;
use function Pest\Laravel\{postJson, assertDatabaseHas, assertAuthenticated};

test('should register a new user successfully with all fields and sanitize data', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'register@example.com',
        'document_type' => DocumentType::CPF->value,
        'document_number' => '123.456.789-01',
        'phone' => '(11) 98888-7777',
        'country_code' => 'BR',
        'birth_date' => '1995-05-20',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = postJson(route('auth.register'), $userData);

    $response->assertStatus(201)
        ->assertJsonPath('message', __('auth.register_success'))
        ->assertJsonStructure(['data' => ['id', 'name', 'email']]);

    assertDatabaseHas('users', [
        'email'           => 'register@example.com',
        'document_number' => '12345678901',
        'phone'           => '11988887777',
        'document_type'   => DocumentType::CPF->value,
    ]);

    assertAuthenticated();
});

test('should register successfully with only mandatory fields', function () {
    $userData = [
        'name' => 'Simple User',
        'email' => 'simple@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = postJson(route('auth.register'), $userData);

    $response->assertStatus(201);
    assertDatabaseHas('users', ['email' => 'simple@example.com']);
});

test('should fail registration if email is already taken', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    postJson(route('auth.register'), [
        'name' => 'New User',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('should fail if password confirmation does not match', function () {
    postJson(route('auth.register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different-password',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('should validate required fields only for mandatory information', function () {
    postJson(route('auth.register'), [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

test('should fail if document_number format is invalid for CPF', function () {
    postJson(route('auth.register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'document_type' => DocumentType::CPF->value,
        'document_number' => '123',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['document_number']);
});

test('should fail if birth_date is in the future', function () {
    postJson(route('auth.register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'birth_date' => now()->addDay()->format('Y-m-d'),
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['birth_date']);
});
