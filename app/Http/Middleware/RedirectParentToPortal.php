<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectParentToPortal
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isParent()) {
            return redirect('/parent');
        }

        return $next($request);
    }
}
