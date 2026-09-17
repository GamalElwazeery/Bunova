<?php

use App\Http\Middleware\AuthenticateDevice;
use App\Http\Middleware\CorrelationIdMiddleware;
use App\Http\Middleware\IdempotencyMiddleware;
use App\Support\Http\ApiErrorResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            CorrelationIdMiddleware::class,
        ]);

        $middleware->alias([
            'idempotent' => IdempotencyMiddleware::class,
            'device.auth' => AuthenticateDevice::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (\Throwable $e) {
            app(\App\Support\Observability\ErrorTracker::class)->captureException($e);
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return ApiErrorResponse::fromException($e, $request);
            }
        });
    })->create();
