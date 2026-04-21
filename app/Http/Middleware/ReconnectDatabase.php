<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ReconnectDatabase
{
    /**
     * Ensure the database connection is alive before processing the request.
     * This prevents 500 errors caused by stale/dropped connections after idle periods.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            try {
                DB::reconnect();
            } catch (\Throwable $reconnectError) {
                // If reconnection also fails, let the request proceed
                // and let the exception handler deal with it
            }
        }

        return $next($request);
    }
}
