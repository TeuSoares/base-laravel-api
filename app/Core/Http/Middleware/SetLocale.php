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
            return $next($request);
        }

        $preferred = $request->getPreferredLanguage();

        if ($preferred) {
            $normalized = str_replace('-', '_', $preferred);

            $language = UserLanguage::tryFrom($normalized);

            if ($language) app()->setLocale($language->value);
        }

        return $next($request);
    }
}
