<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Add deployment routes (for Render free tier without shell access)
            if (file_exists(__DIR__.'/../routes/deploy.php')) {
                require __DIR__.'/../routes/deploy.php';
            }
            // Add diagnostic routes
            if (file_exists(__DIR__.'/../routes/diagnostic.php')) {
                require __DIR__.'/../routes/diagnostic.php';
            }
            // Add setup routes
            if (file_exists(__DIR__.'/../routes/setup.php')) {
                require __DIR__.'/../routes/setup.php';
            }
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust proxies for HTTPS detection (Render, AWS, etc.)
        $middleware->use([
            \App\Http\Middleware\TrustProxies::class,
        ]);
        
        // Add security headers to all responses
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        
        // Add performance monitoring (dev only)
        if (env('APP_ENV') === 'local' || env('APP_ENV') === 'development') {
            $middleware->append(\App\Http\Middleware\PerformanceMonitor::class);
        }
        
        // Register middleware aliases
        $middleware->alias([
            'superadmin' => \App\Http\Middleware\EnsureSuperadmin::class,
            'parent' => \App\Http\Middleware\ParentAccessMiddleware::class,
            'redirect.after.login' => \App\Http\Middleware\RedirectAfterLogin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle Authorization Exceptions (403 Forbidden)
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You do not have permission to perform this action.',
                    'error' => 'Forbidden'
                ], 403);
            }

            return response()->view('errors.403', [
                'exception' => $e,
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        });

        // Handle Model Not Found Exceptions (404 Not Found)
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The requested resource was not found.',
                    'error' => 'Not Found'
                ], 404);
            }

            return response()->view('errors.404', [
                'exception' => $e,
            ], 404);
        });

        // Log all exceptions for debugging
        $exceptions->report(function (\Throwable $e) {
            // Log exception details (Laravel does this by default, but we can customize)
            \Log::error('Exception occurred', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        });
    })->create();
