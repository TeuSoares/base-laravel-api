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
        $user = $request->user();

        if ($user && $user->language instanceof UserLanguage) {
            app()->setLocale($user->language->value);
        } else {
            $locale = $this->parseLocale($request->header('Accept-Language'));

            if (UserLanguage::tryFrom($locale)) {
                app()->setLocale($locale);
            }
        }

        return $next($request);
    }

    private function parseLocale(?string $header): string
    {
        if (!$header) return config('app.locale');

        $firstPart = explode(',', $header)[0];
        $locale = str_replace('-', '_', $firstPart);

        return substr($locale, 0, 5);
    }
}
