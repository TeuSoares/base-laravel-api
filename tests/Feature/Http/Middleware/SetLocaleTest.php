<?php

use App\Models\User;
use App\Core\Enums\UserLanguage;
use Illuminate\Support\Facades\App;
use function Pest\Laravel\{actingAs, postJson};

it('sets the application locale correctly based on header', function (string $header, string $expectedLocale) {
    postJson(route('auth.login'), [], ['Accept-Language' => $header]);

    expect(App::getLocale())->toBe($expectedLocale);
})->with([
    ['pt_BR', 'pt_BR'],
    ['pt-BR', 'pt_BR'],
    ['en', 'en'],
    ['es', 'es'],
]);

it('prioritizes logged user preference over header', function () {
    $user = User::factory()->create(['language' => UserLanguage::EN]);

    actingAs($user)
        ->postJson(route('auth.login'), [], ['Accept-Language' => 'pt_BR']);

    expect(App::getLocale())->toBe('en');
});

it('uses system default when header provides unsupported language', function () {
    postJson(route('auth.login'), [], ['Accept-Language' => 'fr']);

    expect(App::getLocale())->toBe(config('app.locale'));
});
