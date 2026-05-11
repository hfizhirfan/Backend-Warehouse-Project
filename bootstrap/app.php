<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        // ✅ CORS
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

        // ✅ FORCE JSON
        $middleware->append(\App\Http\Middleware\ForceJsonResponse::class);

        // 🔥 TAMBAHKAN INI (WAJIB)
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {

        /**
         * 🔐 HANDLE AUTH ERROR
         * Jangan redirect ke route login
         */
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        });

        /**
         * ⚠️ HANDLE ERROR UMUM
         */
        $exceptions->render(function (\Throwable $e, Request $request) {

            // 🔧 MODE DEVELOPMENT
            if (app()->environment('local')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ], 500);
            }

            // 🔒 MODE PRODUCTION
            return response()->json([
                'message' => 'Server Error'
            ], 500);
        });

    })

    ->create();
