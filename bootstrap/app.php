<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Http\JsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\HandleJwtExceptions::class);

        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
        ]);
    })
    ->withExceptions(function (\Illuminate\Foundation\Configuration\Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, $request) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (TokenExpiredException $e, $request) {
        return new JsonResponse([
                'status' => 'error',
                'message' => 'Token has expired',
                'errors' => [
                    'message' => 'Token has expired'
                ]
            ], 401);
        });

        // Token tidak valid
        $exceptions->render(function (TokenInvalidException $e, $request) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid token',
                'errors' => [
                    'message' => 'Invalid Token'
                ]
            ], 401);
        });

        // Token sudah dikeluarkan dari blacklist
        $exceptions->render(function (TokenBlacklistedException $e, $request) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Token has been blacklisted',
                'errors' => [
                    'message' => 'Token has been blacklisted'
                ]
            ], 401);
        });

        // Fallback umum untuk semua JWT error
        $exceptions->render(function (JWTException $e, $request) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'JWT error: ' . $e->getMessage(),
                'errors' => [
                    'message' => 'JWT error: ' . $e->getMessage()
                ]
            ], 401);
        });

        $exceptions->render(function (RouteNotFoundException $e, $request) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Route not found',
                'errors' => [
                    'message' => 'Route not found'
                ]
            ], 404);
        });
    })->create();
