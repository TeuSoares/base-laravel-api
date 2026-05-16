<?php

use App\Models\User;
use App\Core\Enums\UserLanguage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use function Pest\Laravel\{actingAs, getJson, withCookie, withUnencryptedCookie};

beforeEach(function () {
    // Create a lightweight temporary route that simply returns the application's current locale
    Route::get('/_test/locale', function () {
        return response()->json(['locale' => App::getLocale()]);
    })->middleware(\App\Core\Http\Middleware\SetLocale::class); // Ensures your middleware runs on it
});

it('prioritizes logged user preference over header and browser', function () {
    // Ensure the user prefers English
    $user = User::factory()->create(['language' => UserLanguage::EN]);

    $response = actingAs($user)->getJson('/_test/locale', [
        'X-User-Language' => 'es',
        'Accept-Language' => 'pt-BR'
    ]);

    $response->assertJson(['locale' => 'en']);
});

it('prioritizes X-User-Language header over cookie and browser', function () {
    $response = getJson('/_test/locale', [
        'X-User-Language' => 'en', // Requesting English via custom header
        'Accept-Language' => 'pt-BR'
    ]);

    $response->assertJson(['locale' => 'en']);
});

it('prioritizes X-User-Language header over browser accept-language', function () {
    // Send the preferred language via custom header, ignoring the browser fallback
    $response = getJson('/_test/locale', [
        'X-User-Language' => 'pt_BR',
        'Accept-Language' => 'en-US'
    ]);

    $response->assertJson(['locale' => 'pt_BR']);
});

it('uses browser preference when no user, no custom header and no cookie', function () {
    $response = getJson('/_test/locale', [
        'Accept-Language' => 'pt-BR,pt;q=0.9,en;q=0.8'
    ]);

    // The middleware should extract 'pt', convert it to 'pt_BR', and apply it
    $response->assertJson(['locale' => 'pt_BR']);
});

it('falls back to default for unsupported languages', function () {
    $default = config('app.locale'); // e.g., 'en' or 'pt_BR'

    $response = getJson('/_test/locale', [
        'X-User-Language' => 'fr', // French is not supported
        'Accept-Language' => 'it'  // Italian is not supported either
    ]);

    $response->assertJson(['locale' => $default]);
});
