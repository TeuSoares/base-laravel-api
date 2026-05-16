<?php

namespace App\Core\Http\Middleware;

use App\Core\Enums\UserLanguage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        $user = $request->user();
        if ($user && isset($user->language)) {
            $locale = $user->language instanceof UserLanguage
                ? $user->language->value
                : $user->language;
        }

        if (!$locale) {
            $locale = $request->header('X-User-Language') ?? $request->cookie('user_language');
        }

        if (!$locale) {
            $availableLocales = array_map(fn($lang) => str_replace('_', '-', $lang), UserLanguage::values());

            $preferred = $request->getPreferredLanguage($availableLocales);

            $locale = $preferred ? str_replace('-', '_', $preferred) : null;
        }

        $locale = $locale ?? config('app.locale');

        if (in_array($locale, UserLanguage::values())) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
