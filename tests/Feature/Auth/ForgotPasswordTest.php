<?php

use App\Models\User;
use App\Modules\Auth\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\{postJson};

test('should return success message even if email does not exist', function () {
    $email = 'nonexistent@example.com';

    postJson(route('auth.forgot-password'), ['email' => $email])
        ->assertOk()
        ->assertJsonPath('message', __('auth.forgot_password_info', ['email' => $email]));
});

test('should send reset link notification if user exists', function () {
    Notification::fake();

    $user = User::factory()->create(['email' => 'registered@example.com']);

    postJson(route('auth.forgot-password'), ['email' => $user->email])
        ->assertOk();

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

test('should validate that email is required and valid', function () {
    postJson(route('auth.forgot-password'), ['email' => ''])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);

    postJson(route('auth.forgot-password'), ['email' => 'invalid-email'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('should fail if reset link is requested too soon (throttle)', function () {
    $user = User::factory()->create(['email' => 'throttle@example.com']);

    // First request
    postJson(route('auth.forgot-password'), ['email' => $user->email])->assertOk();

    // Second request immediate (Laravel default throttle)
    postJson(route('auth.forgot-password'), ['email' => $user->email])
        ->assertStatus(429);
});
