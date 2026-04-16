<?php

use App\Models\User;
use App\Core\Enums\UserLanguage;
use Illuminate\Support\Facades\App;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

it('sets the application locale to portuguese when Accept-Language header is pt_BR', function () {
    postJson(route('auth.login'), [], ['Accept-Language' => 'pt_BR'])
        ->assertJsonValidationErrors(['email'])
        ->assertJsonPath('errors.email.0', 'O campo endereço de e-mail é obrigatório.');

    expect(App::getLocale())->toBe('pt_BR');
});

it('sets the application locale to english when Accept-Language header is en', function () {
    postJson(route('auth.login'), [], ['Accept-Language' => 'en'])
        ->assertJsonValidationErrors(['email'])
        ->assertJsonPath('errors.email.0', 'The email address field is required.');

    expect(App::getLocale())->toBe('en');
});

it('normalizes hyphenated locales from browser to underscore format', function () {
    postJson(route('auth.login'), [], ['Accept-Language' => 'pt-BR']);

    expect(App::getLocale())->toBe('pt_BR');
});

it('prioritizes user language preference over Accept-Language header', function () {
    /** @var User $user */
    $user = User::factory()->create(['language' => UserLanguage::EN]);

    actingAs($user)
        ->postJson(route('auth.login'), [], ['Accept-Language' => 'pt_BR']);

    expect(App::getLocale())->toBe('en');
});

it('uses header locale if logged user has no language defined', function () {
    /** @var User $user */
    $user = User::factory()->create(['language' => null]);

    actingAs($user)
        ->postJson(route('auth.login'), [], ['Accept-Language' => 'pt_BR']);

    expect(App::getLocale())->toBe('pt_BR');
});

it('uses fallback locale from config when an unsupported language is provided', function () {
    postJson(route('auth.login'), [], ['Accept-Language' => 'fr']);

    expect(App::getLocale())->toBe(config('app.locale'));
});
