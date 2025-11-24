<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\Logger::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to access this resource.',
                'data' => null,
                'error' => $e->getMessage()
            ], 401);
        });

        $exceptions->render(function (UnauthorizedException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action.',
                'data' => null,
                'error' => $e->getMessage()
            ], 403);
        });
    })->create();
