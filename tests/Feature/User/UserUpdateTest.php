<?php

use App\Core\Http\Middleware\Subscribed;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\{actingAs, assertDatabaseHas, patchJson};

uses()->beforeEach(function () {
    $this->withoutMiddleware(Subscribed::class);
})->in(__FILE__);

function createUserForUpdate(array $overrides = []): User
{
    /** @var User $user */
    $user = User::factory()->create(array_merge(['language' => 'en'], $overrides));
    return $user;
}

function validUpdatePayload(array $overrides = []): array
{
    return array_merge([
        'name'     => 'John Doe',
        'email'    => 'john@example.com',
        'language' => 'en',
    ], $overrides);
}

// ─── Authentication ───────────────────────────────────────────────────────────

test('should require authentication to update user', function () {
    patchJson(route('user.update'), validUpdatePayload())
        ->assertUnauthorized();
});

// ─── Successful updates ───────────────────────────────────────────────────────

test('should update name, email and language successfully', function () {
    $user = createUserForUpdate();

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload([
            'name'     => 'Jane Doe',
            'email'    => 'jane@example.com',
            'language' => 'pt_BR',
        ]))
        ->assertOk()
        ->assertJsonPath('message', __('user.updated'));

    assertDatabaseHas('users', [
        'id'       => $user->id,
        'name'     => 'Jane Doe',
        'email'    => 'jane@example.com',
        'language' => 'pt_BR',
    ]);
});

test('should update password when provided', function () {
    $user = createUserForUpdate(['password' => 'password']);

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload([
            'current_password'      => 'password',
            'password'              => 'Xk9#mP2$vQr8',
            'password_confirmation' => 'Xk9#mP2$vQr8',
        ]))
        ->assertOk();

    expect(Hash::check('Xk9#mP2$vQr8', $user->fresh()->password))->toBeTrue();
});

test('should keep existing password when not provided', function () {
    $user = createUserForUpdate();
    $originalPassword = $user->password;

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload())
        ->assertOk();

    expect($user->fresh()->password)->toBe($originalPassword);
});

test('should accept own email without unique violation', function () {
    $user = createUserForUpdate(['email' => 'john@example.com']);

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload(['email' => 'john@example.com']))
        ->assertOk();
});

test('should trim and lowercase email before saving', function () {
    $user = createUserForUpdate();

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload(['email' => '  JOHN@EXAMPLE.COM  ']))
        ->assertOk();

    assertDatabaseHas('users', [
        'id'    => $user->id,
        'email' => 'john@example.com',
    ]);
});

// ─── Validation ───────────────────────────────────────────────────────────────

test('should fail if name is missing', function () {
    $user = createUserForUpdate();

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload(['name' => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('should fail if email is missing', function () {
    $user = createUserForUpdate();

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload(['email' => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('should fail if email format is invalid', function () {
    $user = createUserForUpdate();

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload(['email' => 'not-an-email']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('should fail if email is already taken by another user', function () {
    User::factory()->create(['email' => 'taken@example.com']);
    $user = createUserForUpdate();

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload(['email' => 'taken@example.com']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('should fail if language is not supported', function () {
    $user = createUserForUpdate();

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload(['language' => 'invalid']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['language']);
});

test('should fail if password has less than 10 characters', function () {
    $user = createUserForUpdate();

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload([
            'password'              => 'Short12',
            'password_confirmation' => 'Short12',
        ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('should fail if password does not contain letters and numbers', function () {
    $user = createUserForUpdate();

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload([
            'password'              => '12345678910',
            'password_confirmation' => '12345678910',
        ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload([
            'password'              => 'OnlyLettersPass',
            'password_confirmation' => 'OnlyLettersPass',
        ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('should fail if password is compromised', function () {
    $user = createUserForUpdate();

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload([
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('should fail if password confirmation does not match', function () {
    $user = createUserForUpdate();

    actingAs($user)
        ->patchJson(route('user.update'), validUpdatePayload([
            'password'              => 'NewPassword123',
            'password_confirmation' => 'WrongConfirmation',
        ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});
