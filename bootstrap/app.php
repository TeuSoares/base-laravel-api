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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
            Log::error($e->getMessage(), [
                'exception' => $e::class,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'route' => request()->fullUrl(),
                'method' => request()->method(),
                'input' => request()->except(['password', 'password_confirmation']),
                'trace' => $e->getTraceAsString(),
            ]);
        });

        $exceptions->render(function (BaseApiException $e) {
            return responseBuilder()->error($e->getPayload(), $e->getStatusCode());
        });

        $exceptions->render(function (ValidationException $e) {
            return responseBuilder()->error($e->errors(), 422, __('exceptions.validation_failed'));
        });

        $exceptions->render(function (AuthenticationException $e) {
            return responseBuilder()->error(['unauthenticated' => __('exceptions.unauthenticated')], 401);
        });

        $exceptions->render(function (QueryException $e) {
            return responseBuilder()->error(['internal' => __('exceptions.query_error')], 500);
        });

        $exceptions->render(function (Throwable $e) {
            return responseBuilder()->error(['internal' => __('exceptions.internal_error')], 500);
        });
    })->create();
