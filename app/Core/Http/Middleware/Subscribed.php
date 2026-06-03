<?php

namespace App\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Subscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->subscribed('default')) {
            return responseBuilder()->error(
                message: __('billing.not_subscribed'),
                status: Response::HTTP_FORBIDDEN,
            );
        }

        return $next($request);
    }
}
