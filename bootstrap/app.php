<?php

use App\Core\Exceptions\BaseApiException;
use App\Core\Http\Middleware\SetLocale;
use App\Core\Http\Middleware\Subscribed;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(SetLocale::class);

        $middleware->statefulApi();

        $middleware->api(append: [
            ThrottleRequests::class . ':api',
        ]);

        $middleware->alias([
            'subscribed' => Subscribed::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $e) {
            $errorId = (string) Str::uuid();

            Log::error("ID: [{$errorId}] - " . $e->getMessage(), [
                'exception' => $e::class,
                'user_id'   => Auth::id() ?? 'guest',
                'url'       => request()->fullUrl(),
                'method'    => request()->method(),
                'ip'        => request()->ip(),
                'input'     => request()->except(['password', 'password_confirmation', 'token', 'credit_card']),
                'file'      => $e->getFile() . ':' . $e->getLine(),
            ]);

            return false;
        });

        $exceptions->render(function (ValidationException $e) {
            return responseBuilder()->error(__('exceptions.validation_failed'), $e->errors(), status: 422);
        });

        $exceptions->render(function (AuthenticationException $e) {
            return responseBuilder()->error(__('exceptions.unauthenticated'), status: 401);
        });

        $exceptions->render(function (BaseApiException $e) {
            return responseBuilder()->error(
                message: $e->getMessage(),
                error: $e->getDetails(),
                status: $e->getStatusCode()
            );
        });

        $exceptions->render(function (QueryException $e) {
            return responseBuilder()->error(__('exceptions.query_error'), status: 500);
        });

        $exceptions->render(function (Throwable $e) {
            $message = config('app.debug')
                ? $e->getMessage()
                : __('exceptions.internal_error');

            return responseBuilder()->error($message, status: 500);
        });
    })->create();
