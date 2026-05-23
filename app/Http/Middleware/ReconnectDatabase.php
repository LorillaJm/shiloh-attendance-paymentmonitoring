<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            // Test the connection
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            Log::warning('Database connection lost, attempting reconnect: ' . $e->getMessage());
            
            try {
                DB::reconnect();
                Log::info('Database reconnected successfully');
            } catch (\Throwable $reconnectError) {
                Log::error('Database reconnection failed: ' . $reconnectError->getMessage());
                // Let the request proceed and let the exception handler deal with it
            }
        }

        return $next($request);
    }
}
