<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request): RedirectResponse
    {
        $user = auth()->user();
        
        // Redirect based on user role
        if ($user->isParent()) {
            return Redirect::intended('/parent');
        }
        
        if ($user->isAdmin() || $user->isSuperadmin()) {
            return Redirect::intended('/admin');
        }
        
        // Default fallback
        return Redirect::intended('/admin');
    }
}
