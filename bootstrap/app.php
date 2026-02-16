<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
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
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
