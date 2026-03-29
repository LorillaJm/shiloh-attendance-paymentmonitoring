<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAfterLogin
{
    /**
     * Handle an incoming request.
     * Redirect users to their appropriate panel based on role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Only redirect if user exists
            if ($user) {
                // Redirect parents to parent portal
                if ($user->isParent()) {
                    // If trying to access admin panel, redirect to parent portal
                    if ($request->is('admin') || $request->is('admin/*')) {
                        return redirect('/parent');
                    }
                }
                
                // Redirect admins/superadmins to admin panel
                if ($user->isAdmin() || $user->isSuperadmin()) {
                    // If trying to access parent portal, redirect to admin
                    if ($request->is('parent') || $request->is('parent/*')) {
                        return redirect('/admin');
                    }
                }
            }
        }

        return $next($request);
    }
}
