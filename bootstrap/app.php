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
        
        // Reconnect stale database connections (prevents 500s after idle)
        $middleware->append(\App\Http\Middleware\ReconnectDatabase::class);
        
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

        // Handle CSRF Token Mismatch / Session Expired (419)
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->header('X-Livewire')) {
                return response()->json([
                    'message' => 'Your session has expired. Please refresh the page.',
                ], 419);
            }

            return response()->view('errors.419', [], 419);
        });

        // Handle database connection errors gracefully
        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            $isConnectionError = in_array($e->getCode(), ['08006', '08001', '08004', '57P01', 'HY000', '2002']);
            if ($isConnectionError) {
                // Try to reconnect once
                try {
                    \Illuminate\Support\Facades\DB::reconnect();
                } catch (\Throwable $reconnectError) {
                    // Reconnect failed
                }

                if ($request->expectsJson() || $request->header('X-Livewire')) {
                    return response()->json([
                        'message' => 'A temporary database error occurred. Please try again.',
                    ], 503);
                }

                return response()->view('errors.500', [
                    'exception' => $e,
                ], 503);
            }

            return null; // Let other query exceptions pass through
        });

        // Laravel already logs all exceptions by default via the exception handler
    })->create();
