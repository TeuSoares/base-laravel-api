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
            $acceptLanguage = $request->header('Accept-Language');

            if ($acceptLanguage) {
                $firstLanguage = explode(',', $acceptLanguage)[0];
                $baseLanguage = preg_split('/[-_]/', trim($firstLanguage))[0];

                if ($baseLanguage === 'pt') {
                    $locale = 'pt_BR';
                } elseif (in_array($baseLanguage, UserLanguage::values())) {
                    $locale = $baseLanguage;
                }
            }
        }

        $locale = $locale ?? config('app.locale');

        if (in_array($locale, UserLanguage::values())) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
