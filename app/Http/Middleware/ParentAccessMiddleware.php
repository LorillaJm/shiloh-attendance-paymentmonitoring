<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParentAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isParent()) {
            abort(403, 'Access denied. This area is for parents only.');
        }

        return $next($request);
    }
}
