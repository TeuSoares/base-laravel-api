<?php

use App\Models\User;
use App\Core\Enums\UserLanguage;
use Illuminate\Support\Facades\App;
use function Pest\Laravel\{actingAs, postJson};

beforeEach(function () {
    App::setLocale('en');
});

it('prioritizes logged user preference over header and browser', function () {
    $user = User::factory()->create(['language' => UserLanguage::EN]);

    actingAs($user)->postJson(route('auth.login'), [], [
        'X-User-Language' => 'es',
        'Accept-Language' => 'pt_BR'
    ]);

    expect(App::getLocale())->toBe('en');
});

it('prioritizes X-User-Language header over browser accept-language', function () {
    postJson(route('auth.login'), [], [
        'X-User-Language' => 'es',
        'Accept-Language' => 'pt_BR'
    ]);

    expect(App::getLocale())->toBe('es');
});

it('uses browser preference when no user and no custom header', function () {
    postJson(route('auth.login'), [], [
        'Accept-Language' => 'pt-BR'
    ]);

    expect(App::getLocale())->toBe('pt_BR');
});

it('falls back to default for unsupported languages', function () {
    $default = config('app.locale');

    postJson(route('auth.login'), [], [
        'X-User-Language' => 'fr',
        'Accept-Language' => 'it'
    ]);

    expect(App::getLocale())->toBe($default);
});
